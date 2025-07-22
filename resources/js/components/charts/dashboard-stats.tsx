"use client"

import * as React from "react"
import axios from "axios"
import StatsChart from "./stats-chart"

type DashboardStatsProps = {
  from?: Date;
  to?: Date;
  server?: string;
};

type AllStatsData = {
  download: {
    latest: number | null;
    average: number | null;
  };
  upload: {
    latest: number | null;
    average: number | null;
  };
  ping: {
    latest: number | null;
    average: number | null;
  };
};

export default function DashboardStats({ from, to, server }: DashboardStatsProps) {
  const [stats, setStats] = React.useState<AllStatsData | null>(null);
  const [loading, setLoading] = React.useState(true);

  React.useEffect(() => {
    const fetchStats = async () => {
      setLoading(true);
      try {
        const params = new URLSearchParams();
        if (from) params.append('from', from.toISOString());
        if (to) params.append('to', to.toISOString());
        if (server) params.append('server', server);

        const response = await axios.get(`/api/results/stats?${params}`);
        setStats(response.data);
      } catch (error) {
        console.error('Error fetching stats:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchStats();
  }, [from, to, server]);

  if (loading) {
    return (
      <div className="space-y-6">
        {[1, 2, 3].map((i) => (
          <div key={i} className="grid grid-cols-2 gap-4 animate-pulse">
            {[1, 2, 3, 4].map((j) => (
              <div key={j} className="h-24 bg-muted rounded-lg"></div>
            ))}
          </div>
        ))}
      </div>
    );
  }

  if (!stats) {
    return <div>Error loading statistics</div>;
  }

  return (
    <div className="space-y-6">
      <div>
        <h3 className="font-medium mb-4">Download Statistics</h3>
        <StatsChart type="download" stats={stats.download} />
      </div>
      
      <div>
        <h3 className="font-medium mb-4">Upload Statistics</h3>
        <StatsChart type="upload" stats={stats.upload} />
      </div>
      
      <div>
        <h3 className="font-medium mb-4">Ping Statistics</h3>
        <StatsChart type="ping" stats={stats.ping} />
      </div>
    </div>
  );
} 