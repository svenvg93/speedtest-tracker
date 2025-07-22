import * as React from "react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogFooter,
  DialogClose,
} from "@/components/ui/dialog";
import { Play, Loader2 } from "lucide-react";
import { router } from '@inertiajs/react';
import axios from "axios";

interface Server {
  [key: string]: string;
}

interface SpeedtestDialogProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
}

export default function SpeedtestDialog({ open, onOpenChange }: SpeedtestDialogProps) {
  const [servers, setServers] = React.useState<Server>({});
  const [selectedServer, setSelectedServer] = React.useState<string>('auto');
  const [loading, setLoading] = React.useState(false);
  const [serversLoading, setServersLoading] = React.useState(false);

  React.useEffect(() => {
    if (open) {
      fetchServers();
    }
  }, [open]);

  const fetchServers = async () => {
    setServersLoading(true);
    try {
      const response = await axios.get('/speedtest/servers');
      setServers(response.data.servers);
    } catch (error) {
      console.error('Failed to fetch servers:', error);
      setServers({ 'error': 'Failed to load servers' });
    } finally {
      setServersLoading(false);
    }
  };

  const handleStartSpeedtest = () => {
    setLoading(true);
    
    const data = selectedServer && selectedServer !== 'auto' ? { server_id: selectedServer } : {};
    
    router.post('/speedtest/run', data, {
      onSuccess: () => {
        setLoading(false);
        setSelectedServer('auto');
        onOpenChange(false);
      },
      onError: () => {
        setLoading(false);
      }
    });
  };

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="sm:max-w-md">
        <DialogHeader>
          <DialogTitle>Run Speedtest</DialogTitle>
        </DialogHeader>
        
                  <div className="space-y-4">
            <div>
              <Label htmlFor="server" className="mb-2 block">Select Server</Label>
              <Select 
                value={selectedServer} 
                onValueChange={setSelectedServer}
                disabled={serversLoading}
              >
              <SelectTrigger>
                <SelectValue placeholder={serversLoading ? "Loading servers..." : "Leave empty for automatic selection"} />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="auto">Automatic (closest server)</SelectItem>
                {serversLoading ? (
                  <SelectItem value="loading" disabled>
                    <div className="flex items-center gap-2">
                      <Loader2 className="h-4 w-4 animate-spin" />
                      Loading servers...
                    </div>
                  </SelectItem>
                ) : (
                  Object.entries(servers).map(([id, name]) => (
                    <SelectItem key={id} value={id}>
                      {name}
                    </SelectItem>
                  ))
                )}
              </SelectContent>
            </Select>
          </div>
        </div>

        <DialogFooter>
          <DialogClose asChild>
            <Button variant="outline" disabled={loading}>
              Cancel
            </Button>
          </DialogClose>
          <Button 
            onClick={handleStartSpeedtest} 
            disabled={loading || serversLoading}
            className="bg-primary text-primary-foreground hover:bg-primary/90 hover:text-primary-foreground active:bg-primary/90 active:text-primary-foreground duration-200 ease-linear"
          >
            {loading ? (
              <>
                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                Starting...
              </>
            ) : (
              <>
                <Play className="mr-2 h-4 w-4" />
                Start Speedtest
              </>
            )}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
} 