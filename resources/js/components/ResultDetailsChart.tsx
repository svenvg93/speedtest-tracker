import * as React from "react";
import { Button } from "@/components/ui/button";
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetFooter } from "@/components/ui/sheet";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { ChevronDown, ChevronRight } from "lucide-react";

export type Result = {
  id: number;
  service: string;
  ping: number | null;
  download: number | null;
  upload: number | null;
  status: string;
  created_at: string;
  updated_at?: string;
  scheduled?: boolean;
  healthy?: boolean;
  data: any;
  result_url?: string;
  comments?: string;
};

interface ResultDetailsChartProps {
  result: Result | null;
  open: boolean;
  onOpenChange: (open: boolean) => void;
}

export default function ResultDetailsChart({ result, open, onOpenChange }: ResultDetailsChartProps) {
  const [downloadLatencyOpen, setDownloadLatencyOpen] = React.useState(false);
  const [uploadLatencyOpen, setUploadLatencyOpen] = React.useState(false);
  const [pingDetailsOpen, setPingDetailsOpen] = React.useState(false);
  const [serverMetadataOpen, setServerMetadataOpen] = React.useState(false);

  if (!result) return null;

  return (
    <Sheet open={open} onOpenChange={onOpenChange}>
      <SheetContent className="w-full overflow-y-auto p-6">
        <SheetHeader>
          <SheetTitle>Result Details</SheetTitle>
        </SheetHeader>
        
        <div className="space-y-6">
          {/* Main Content */}
          <div className="space-y-6">
            {/* Result Overview */}
            <Card>
              <CardHeader>
                <CardTitle>Result Overview</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="text-sm font-medium text-gray-600 dark:text-gray-400">ID</label>
                  <Input value={result.id} readOnly className="text-sm" />
                </div>
                <div>
                  <label className="text-sm font-medium text-gray-600 dark:text-gray-400">Created</label>
                  <Input value={new Date(result.created_at).toLocaleString()} readOnly className="text-sm" />
                </div>
                <div>
                  <label className="text-sm font-medium text-gray-600 dark:text-gray-400">Download</label>
                  <Input 
                    value={result.download ? `${((Number(result.download) * 8) / 1_000_000).toFixed(2)} Mbit` : ''} 
                    readOnly 
                    className="text-sm" 
                  />
                </div>
                <div>
                  <label className="text-sm font-medium text-gray-600 dark:text-gray-400">Upload</label>
                  <Input 
                    value={result.upload ? `${((Number(result.upload) * 8) / 1_000_000).toFixed(2)} Mbit` : ''} 
                    readOnly 
                    className="text-sm" 
                  />
                </div>
                <div>
                  <label className="text-sm font-medium text-gray-600 dark:text-gray-400">Ping</label>
                  <Input 
                    value={result.ping ? `${Math.round(Number(result.ping))} ms` : ''} 
                    readOnly 
                    className="text-sm" 
                  />
                </div>
                <div>
                  <label className="text-sm font-medium text-gray-600 dark:text-gray-400">Packet Loss</label>
                  <Input 
                    value={result.data?.packetLoss ? `${Number(result.data.packetLoss).toFixed(2)} %` : '0.00%'} 
                    readOnly 
                    className="text-sm" 
                  />
                </div>
              </div>
              </CardContent>
            </Card>

            {/* Download Latency */}
            <Card>
              <CardHeader>
                <button
                  onClick={() => setDownloadLatencyOpen(!downloadLatencyOpen)}
                  className="flex items-center justify-between w-full text-left"
                >
                  <CardTitle>Download Latency</CardTitle>
                  {downloadLatencyOpen ? <ChevronDown className="h-5 w-5" /> : <ChevronRight className="h-5 w-5" />}
                </button>
              </CardHeader>
              {downloadLatencyOpen && (
                <CardContent>
                  <div className="grid grid-cols-2 gap-4">
                  <div>
                    <label className="text-sm font-medium text-gray-600 dark:text-gray-400">Jitter</label>
                    <Input 
                      value={result.data?.download?.latency?.jitter ? `${Math.round(Number(result.data.download.latency.jitter))} ms` : ''} 
                      readOnly 
                      className="text-sm" 
                    />
                  </div>
                  <div>
                    <label className="text-sm font-medium text-gray-600 dark:text-gray-400">High</label>
                    <Input 
                      value={result.data?.download?.latency?.high ? `${Math.round(Number(result.data.download.latency.high))} ms` : ''} 
                      readOnly 
                      className="text-sm" 
                    />
                  </div>
                  <div>
                    <label className="text-sm font-medium text-gray-600 dark:text-gray-400">Low</label>
                    <Input 
                      value={result.data?.download?.latency?.low ? `${Math.round(Number(result.data.download.latency.low))} ms` : ''} 
                      readOnly 
                      className="text-sm" 
                    />
                  </div>
                  <div>
                    <label className="text-sm font-medium text-gray-600 dark:text-gray-400">IQM</label>
                    <Input 
                      value={result.data?.download?.latency?.iqm ? `${Math.round(Number(result.data.download.latency.iqm))} ms` : ''} 
                      readOnly 
                      className="text-sm" 
                    />
                  </div>
                </div>
                </CardContent>
              )}
            </Card>

            {/* Upload Latency */}
            <Card>
              <CardHeader>
                <button
                  onClick={() => setUploadLatencyOpen(!uploadLatencyOpen)}
                  className="flex items-center justify-between w-full text-left"
                >
                  <CardTitle>Upload Latency</CardTitle>
                  {uploadLatencyOpen ? <ChevronDown className="h-5 w-5" /> : <ChevronRight className="h-5 w-5" />}
                </button>
              </CardHeader>
              {uploadLatencyOpen && (
                <CardContent>
                  <div className="grid grid-cols-2 gap-4">
                                <div>
                  <label className="text-sm font-medium text-gray-600 dark:text-gray-400">Jitter</label>
                  <Input 
                    value={result.data?.upload?.latency?.jitter ? `${Math.round(Number(result.data.upload.latency.jitter))} ms` : ''} 
                    readOnly 
                    className="text-sm" 
                  />
                </div>
                <div>
                  <label className="text-sm font-medium text-gray-600 dark:text-gray-400">High</label>
                  <Input 
                    value={result.data?.upload?.latency?.high ? `${Math.round(Number(result.data.upload.latency.high))} ms` : ''} 
                    readOnly 
                    className="text-sm" 
                  />
                </div>
                <div>
                  <label className="text-sm font-medium text-gray-600 dark:text-gray-400">Low</label>
                  <Input 
                    value={result.data?.upload?.latency?.low ? `${Math.round(Number(result.data.upload.latency.low))} ms` : ''} 
                    readOnly 
                    className="text-sm" 
                  />
                </div>
                <div>
                  <label className="text-sm font-medium text-gray-600 dark:text-gray-400">IQM</label>
                  <Input 
                    value={result.data?.upload?.latency?.iqm ? `${Math.round(Number(result.data.upload.latency.iqm))} ms` : ''} 
                    readOnly 
                    className="text-sm" 
                  />
                </div>
              </div>
                </CardContent>
              )}
            </Card>

            {/* Ping Details */}
            <Card>
              <CardHeader>
                <button
                  onClick={() => setPingDetailsOpen(!pingDetailsOpen)}
                  className="flex items-center justify-between w-full text-left"
                >
                  <CardTitle>Ping Details</CardTitle>
                  {pingDetailsOpen ? <ChevronDown className="h-5 w-5" /> : <ChevronRight className="h-5 w-5" />}
                </button>
              </CardHeader>
              {pingDetailsOpen && (
                <CardContent>
                  <div className="grid grid-cols-2 gap-4">
                  <div>
                    <label className="text-sm font-medium text-gray-600 dark:text-gray-400">Jitter</label>
                    <Input 
                      value={result.data?.ping?.jitter ? `${Math.round(Number(result.data.ping.jitter))} ms` : ''} 
                      readOnly 
                      className="text-sm" 
                    />
                  </div>
                  <div>
                    <label className="text-sm font-medium text-gray-600 dark:text-gray-400">Low</label>
                    <Input 
                      value={result.data?.ping?.low ? `${Math.round(Number(result.data.ping.low))} ms` : ''} 
                      readOnly 
                      className="text-sm" 
                    />
                  </div>
                  <div>
                    <label className="text-sm font-medium text-gray-600 dark:text-gray-400">High</label>
                    <Input 
                      value={result.data?.ping?.high ? `${Math.round(Number(result.data.ping.high))} ms` : ''} 
                      readOnly 
                      className="text-sm" 
                    />
                  </div>
                </div>
                </CardContent>
              )}
            </Card>


          {/* Server & Metadata */}
          <Card>
            <CardHeader>
              <button
                onClick={() => setServerMetadataOpen(!serverMetadataOpen)}
                className="flex items-center justify-between w-full text-left"
              >
                <CardTitle>Server & Metadata</CardTitle>
                {serverMetadataOpen ? <ChevronDown className="h-5 w-5" /> : <ChevronRight className="h-5 w-5" />}
              </button>
            </CardHeader>
            {serverMetadataOpen && (
              <CardContent>
                <div className="space-y-4">
                <div>
                  <label className="text-sm font-medium text-gray-600 dark:text-gray-400">Service</label>
                  <p className="text-sm">{result.service}</p>
                </div>
                <div>
                  <label className="text-sm font-medium text-gray-600 dark:text-gray-400">Server Name</label>
                  <p className="text-sm">{result.data?.server?.name || ''}</p>
                </div>
                <div>
                  <label className="text-sm font-medium text-gray-600 dark:text-gray-400">Server ID</label>
                  <p className="text-sm">{result.data?.server?.id || ''}</p>
                </div>
                <div>
                  <label className="text-sm font-medium text-gray-600 dark:text-gray-400">ISP</label>
                  <p className="text-sm">{result.data?.isp || ''}</p>
                </div>
                <div>
                  <label className="text-sm font-medium text-gray-600 dark:text-gray-400">Server Location</label>
                  <p className="text-sm">{result.data?.server?.location || ''}</p>
                </div>
                <div>
                  <label className="text-sm font-medium text-gray-600 dark:text-gray-400">Server Host</label>
                  <p className="text-sm">{result.data?.server?.host || ''}</p>
                </div>
                <div>
                  <label className="text-sm font-medium text-gray-600 dark:text-gray-400">Comments</label>
                  <p className="text-sm">{result.comments || ''}</p>
                </div>
                <div className="flex items-center space-x-2">
                  <input
                    type="checkbox"
                    checked={result.scheduled || false}
                    readOnly
                    className="rounded"
                  />
                  <label className="text-sm font-medium text-gray-600 dark:text-gray-400">Scheduled</label>
                </div>
                <div className="flex items-center space-x-2">
                  <input
                    type="checkbox"
                    checked={result.healthy || false}
                    readOnly
                    className="rounded"
                  />
                                    <label className="text-sm font-medium text-gray-600 dark:text-gray-400">Healthy</label>
                </div>
              </div>
                </CardContent>
            )}
          </Card>
            {/* Message */}
            <Card>
              <CardHeader>
                <CardTitle>Message</CardTitle>
              </CardHeader>
              <CardContent>
                <textarea 
                  value={result.data?.message || 'No message'} 
                  readOnly 
                  className="w-full min-h-[80px] p-3 text-sm border rounded-md resize-none"
                  rows={3}
                />
                <div className="mt-2 text-xs text-gray-500 dark:text-gray-400">
                  🔗 <a href="https://docs.speedtest-tracker.dev/help/error-messages" target="_blank" rel="nofollow" className="text-blue-600 dark:text-blue-400 hover:underline">Error Messages</a>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
        
        <SheetFooter>
          <Button variant="outline" onClick={() => onOpenChange(false)}>Close</Button>
        </SheetFooter>
      </SheetContent>
    </Sheet>
  );
} 