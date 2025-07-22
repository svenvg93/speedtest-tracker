"use client"

import * as React from "react"
import axios from "axios"

export default function HealthChart({ from, to, server }: { from?: Date; to?: Date; server?: string }) {
  const [data, setData] = React.useState<{ created_at: string; healthy: boolean | null }[]>([]);
  const [healthPercent, setHealthPercent] = React.useState(100);

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
          setHealthPercent(0);
          return;
        }
        const healthyCount = res.data.filter((r: any) => r.healthy).length;
        setHealthPercent(Math.round((healthyCount / res.data.length) * 100));
      });
  }, [from, to, server]);

  const healthyCount = data.filter((r) => r.healthy).length;
  const unhealthyCount = data.filter((r) => !r.healthy).length;
  const totalTests = data.length;

  const getHealthStatus = () => {
    if (totalTests === 0) return "No Data";
    if (healthPercent < 80) return "Poor";
    if (healthPercent < 95) return "Good";
    return "Excellent";
  };

  const getHealthColor = () => {
    if (healthPercent === 0) return "bg-orange-500";
    if (healthPercent < 80) return "bg-red-500";
    if (healthPercent < 95) return "bg-yellow-500";
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
            width: `${healthPercent}%`
          }}
        />
      </div>
      
      {/* Status and Percentage */}
      <div className="flex items-center justify-between">
        <div className="flex items-center space-x-2">
          <div className={`w-3 h-3 rounded-full ${getHealthColor()}`} />
          <span className="text-sm font-medium">{getHealthStatus()}</span>
        </div>
        <div className="text-right">
          <div className="text-lg font-bold">{healthPercent}%</div>
          <div className="text-xs text-muted-foreground">
            {healthyCount} healthy, {unhealthyCount} unhealthy
          </div>
        </div>
      </div>
    </div>
  );
} 