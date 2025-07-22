import * as React from "react";
import { Button } from "@/components/ui/button";
import { Calendar } from "@/components/ui/calendar";
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
import { ChevronDownIcon } from "lucide-react";
import { subDays, subMonths, subYears } from "date-fns";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";

const quickRanges = [
  { label: "Last 24 hours", getRange: () => ({ from: subDays(new Date(), 1), to: new Date() }) },
  { label: "Last 7 days", getRange: () => ({ from: subDays(new Date(), 7), to: new Date() }) },
  { label: "Last 1 month", getRange: () => ({ from: subMonths(new Date(), 1), to: new Date() }) },
  { label: "Last 6 months", getRange: () => ({ from: subMonths(new Date(), 6), to: new Date() }) },
  { label: "Last 1 year", getRange: () => ({ from: subYears(new Date(), 1), to: new Date() }) },
];

export function CalendarRangePopover({ range, setRange }: {
  range: { from: Date | undefined; to: Date | undefined };
  setRange: (r: { from: Date | undefined; to: Date | undefined }) => void;
}) {
  const [open, setOpen] = React.useState(false);
  const [from, setFrom] = React.useState<Date | undefined>(range.from);
  const [fromTime, setFromTime] = React.useState("00:00:00");
  const [to, setTo] = React.useState<Date | undefined>(range.to);
  const [toTime, setToTime] = React.useState("23:59:59");

  // Helper to combine date and time into a Date object
  function combineDateTime(date: Date | undefined, time: string): Date | undefined {
    if (!date) return undefined;
    const [h, m, s] = time.split(":").map(Number);
    const d = new Date(date);
    d.setHours(h || 0, m || 0, s || 0, 0);
    return d;
  }

  function applyRange() {
    setRange({
      from: combineDateTime(from, fromTime),
      to: combineDateTime(to, toTime),
    });
    setOpen(false);
  }

  function handleQuickRange(qr: { from: Date; to: Date }) {
    setFrom(qr.from);
    setFromTime("00:00:00");
    setTo(qr.to);
    setToTime("23:59:59");
    setRange(qr);
    setOpen(false);
  }

  return (
    <Popover open={open} onOpenChange={setOpen}>
      <PopoverTrigger asChild>
        <Button variant="outline" className="w-80 justify-between font-normal">
          {from && to
            ? `${from.toLocaleDateString()} ${fromTime} - ${to.toLocaleDateString()} ${toTime}`
            : "Select date & time range"}
          <ChevronDownIcon />
        </Button>
      </PopoverTrigger>
      <PopoverContent className="w-auto p-4" align="start">
        <div className="flex gap-8">
          <div className="flex flex-col gap-4">
            <div>
              <Label className="mb-1 block">From</Label>
              <div className="flex gap-2 items-end">
                <Popover>
                  <PopoverTrigger asChild>
                    <Button variant="outline" className="w-32 justify-between font-normal">
                      {from ? from.toLocaleDateString() : "Select date"}
                      <ChevronDownIcon />
                    </Button>
                  </PopoverTrigger>
                  <PopoverContent className="w-auto overflow-hidden p-0" align="start">
                    <Calendar
                      mode="single"
                      selected={from}
                      captionLayout="dropdown"
                      onSelect={(date) => setFrom(date)}
                      required={false}
                    />
                  </PopoverContent>
                </Popover>
                <Input
                  type="time"
                  step="1"
                  value={fromTime}
                  onChange={e => setFromTime(e.target.value)}
                  className="bg-background appearance-none w-28"
                />
              </div>
            </div>
            <div>
              <Label className="mb-1 block">Till</Label>
              <div className="flex gap-2 items-end">
                <Popover>
                  <PopoverTrigger asChild>
                    <Button variant="outline" className="w-32 justify-between font-normal">
                      {to ? to.toLocaleDateString() : "Select date"}
                      <ChevronDownIcon />
                    </Button>
                  </PopoverTrigger>
                  <PopoverContent className="w-auto overflow-hidden p-0" align="start">
                    <Calendar
                      mode="single"
                      selected={to}
                      captionLayout="dropdown"
                      onSelect={(date) => setTo(date)}
                      required={false}
                    />
                  </PopoverContent>
                </Popover>
                <Input
                  type="time"
                  step="1"
                  value={toTime}
                  onChange={e => setToTime(e.target.value)}
                  className="bg-background appearance-none w-28"
                />
              </div>
            </div>
            <Button className="mt-2" onClick={applyRange}>Apply</Button>
          </div>
          <div className="flex flex-col gap-2">
            {quickRanges.map(qr => (
              <Button
                key={qr.label}
                variant="ghost"
                size="sm"
                className="px-2"
                onClick={() => handleQuickRange(qr.getRange())}
                type="button"
              >
                {qr.label}
              </Button>
            ))}
          </div>
        </div>
      </PopoverContent>
    </Popover>
  );
} 