"use client"

import * as React from "react"
import axios from "axios"

export default function StatusChart({ from, to, server }: { from?: Date; to?: Date; server?: string }) {
  const [data, setData] = React.useState<{ 
    created_at: string; 
    download: number | null; 
    upload: number | null; 
    ping: number | null; 
    healthy: boolean | null;
    status: string;
  }[]>([]);
  const [completedPercent, setCompletedPercent] = React.useState(100);

  React.useEffect(() => {
    if (!from || !to) return;
    
    axios
      .get("/api/dashboard/results", {
        params: {
          from: from.toISOString(),
          to: to.toISOString(),
          server: server || "all",
        },
      })
      .then((res) => {
        setData(res.data);
        if (!res.data.length) {
          setCompletedPercent(0);
          return;
        }
        const completedCount = res.data.filter((r: any) => r.status === 'completed').length;
        setCompletedPercent(Math.round((completedCount / res.data.length) * 100));
      })
      .catch((error) => {
        console.error('StatusChart - API Error:', error);
      });
  }, [from, to, server]);

  const completedCount = data.filter((r) => r.status === 'completed').length;
  const failedCount = data.filter((r) => r.status !== 'completed').length;
  const totalTests = data.length;

  const getStatusText = () => {
    if (totalTests === 0) return "No Data";
    if (completedPercent < 80) return "Poor";
    if (completedPercent < 95) return "Good";
    return "Excellent";
  };

  const getStatusColor = () => {
    if (completedPercent === 0) return "bg-orange-500";
    if (completedPercent < 80) return "bg-red-500";
    if (completedPercent < 95) return "bg-yellow-500";
    return "bg-green-500";
  };

  return (
    <div className="h-[80px] w-full flex flex-col justify-center space-y-3">
      {/* Progress Bar with Gradient */}
      <div className="relative w-full h-6 bg-gray-200 rounded-full overflow-hidden">
        {/* Progress bar with actual percentage width */}
        <div 
          className="h-full transition-all duration-500 ease-out bg-green-500"
          style={{ 
            width: `${completedPercent}%`
          }}
        />
      </div>
      
      {/* Status and Percentage */}
      <div className="flex items-center justify-between">
        <div className="flex items-center space-x-2">
          <div className={`w-3 h-3 rounded-full ${getStatusColor()}`} />
          <span className="text-sm font-medium">{getStatusText()}</span>
        </div>
        <div className="text-right">
          <div className="text-lg font-bold">{completedPercent}%</div>
          <div className="text-xs text-muted-foreground">
            {completedCount} completed, {failedCount} failed
          </div>
        </div>
      </div>
    </div>
  );
} 