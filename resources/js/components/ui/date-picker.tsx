import * as React from "react";
import { Button } from "@/components/ui/button";
import { Calendar } from "@/components/ui/calendar";
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
import { Input } from "@/components/ui/input";
import { CalendarIcon } from "lucide-react";
import { format } from "date-fns";
import { cn } from "@/lib/utils";

interface DatePickerProps {
  value?: string;
  onChange: (value: string) => void;
  placeholder?: string;
  className?: string;
}

export function DatePicker({ value, onChange, placeholder = "Pick a date and time", className }: DatePickerProps) {
  const [open, setOpen] = React.useState(false);
  const [selectedDate, setSelectedDate] = React.useState<Date | undefined>(
    value ? new Date(value) : undefined
  );
  const [timeValue, setTimeValue] = React.useState(
    value ? format(new Date(value), "HH:mm") : ""
  );
  const containerRef = React.useRef<HTMLDivElement>(null);

  // Update internal state when value prop changes
  React.useEffect(() => {
    if (value) {
      setSelectedDate(new Date(value));
      setTimeValue(format(new Date(value), "HH:mm"));
    } else {
      setSelectedDate(undefined);
      setTimeValue("");
    }
  }, [value]);

  // Helper to combine date and time into a string
  function combineDateTime(date: Date | undefined, time: string): string {
    if (!date) return "";
    const [hours, minutes] = time.split(":").map(Number);
    const combinedDate = new Date(date);
    combinedDate.setHours(hours || 0, minutes || 0, 0, 0);
    return combinedDate.toISOString().slice(0, 16); // Format: YYYY-MM-DDTHH:mm
  }

  function handleDateSelect(date: Date | undefined) {
    setSelectedDate(date);
    if (date && timeValue) {
      onChange(combineDateTime(date, timeValue));
    } else if (date) {
      // If we have a date but no time, set a default time
      const defaultTime = "12:00";
      setTimeValue(defaultTime);
      onChange(combineDateTime(date, defaultTime));
    }
  }

  function handleTimeChange(time: string) {
    setTimeValue(time);
    if (selectedDate && time) {
      onChange(combineDateTime(selectedDate, time));
    }
  }

  function handleClear() {
    setSelectedDate(undefined);
    setTimeValue("");
    onChange("");
  }

  // Handle click outside to close
  React.useEffect(() => {
    function handleClickOutside(event: MouseEvent) {
      if (containerRef.current && !containerRef.current.contains(event.target as Node)) {
        setOpen(false);
      }
    }

    if (open) {
      document.addEventListener('mousedown', handleClickOutside);
      return () => {
        document.removeEventListener('mousedown', handleClickOutside);
      };
    }
  }, [open]);

  return (
    <div className="relative" ref={containerRef}>
      <Button
        variant="outline"
        className={cn(
          "w-full justify-start text-left font-normal",
          !selectedDate && "text-muted-foreground",
          className
        )}
        onClick={() => {
          setOpen(!open);
        }}
      >
        <CalendarIcon className="mr-2 h-4 w-4" />
        {selectedDate ? (
          <>
            {format(selectedDate, "PPP")}
            {timeValue && ` at ${timeValue}`}
          </>
        ) : (
          placeholder
        )}
      </Button>
      
      {open && (
        <div className="absolute top-full left-0 mt-1 w-auto p-4 bg-popover border rounded-md shadow-lg z-[100] min-w-[280px]">
          <div className="space-y-4">
            <Calendar
              mode="single"
              selected={selectedDate}
              onSelect={handleDateSelect}
              initialFocus
              disabled={(date) => date < new Date()}
            />
            <div className="space-y-2">
              <label className="text-sm font-medium">Time</label>
              <Input
                type="time"
                value={timeValue}
                onChange={(e) => handleTimeChange(e.target.value)}
                className="w-full"
              />
            </div>
            <div className="flex gap-2">
              <Button
                variant="outline"
                size="sm"
                onClick={handleClear}
                className="flex-1"
              >
                Clear
              </Button>
              <Button
                size="sm"
                onClick={() => setOpen(false)}
                className="flex-1"
              >
                Done
              </Button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
} 