"use client"

import * as React from "react"
import axios from "axios"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { TrendingUp, TrendingDown, Minus } from "lucide-react"

type StatsChartProps = {
  from?: Date;
  to?: Date;
  server?: string;
  type: 'download' | 'upload' | 'ping';
  stats?: StatsData; // Allow passing stats from parent
};

type StatsData = {
  latest: number | null | undefined;
  average: number | null | undefined;
};

export default function StatsChart({ from, to, server, type, stats: propStats }: StatsChartProps) {
  const [stats, setStats] = React.useState<StatsData>({
    latest: null,
    average: null,
  });
  const [loading, setLoading] = React.useState(!propStats);

  React.useEffect(() => {
    // If stats are passed as props, use them directly
    if (propStats) {
      setStats(propStats);
      setLoading(false);
      return;
    }

    const fetchStats = async () => {
      setLoading(true);
      try {
        const params = new URLSearchParams();
        if (from) params.append('from', from.toISOString());
        if (to) params.append('to', to.toISOString());
        if (server) params.append('server', server);

        const response = await axios.get(`/api/results/stats?${params}`);
        setStats(response.data[type]);
      } catch (error) {
        console.error('Error fetching stats:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchStats();
  }, [from, to, server, type, propStats]);

  const formatValue = (value: number | null | undefined): string => {
    if (value === null || value === undefined) return 'N/A';
    
    if (type === 'ping') {
      return `${value.toFixed(0)} ms`;
    } else {
      // Convert from bits to Mbps for download/upload
      const mbps = (value * 8) / 1_000_000;
      if (mbps >= 1000) {
        return `${(mbps / 1000).toFixed(2)} Gbps`;
      } else {
        return `${mbps.toFixed(2)} Mbps`;
      }
    }
  };

  const getTypeLabel = () => {
    switch (type) {
      case 'download': return 'Download';
      case 'upload': return 'Upload';
      case 'ping': return 'Ping';
      default: return type;
    }
  };

  if (loading) {
    return (
      <div className="grid grid-cols-2 gap-4">
        {[1, 2].map((i) => (
          <Card key={i} className="animate-pulse">
            <CardContent className="p-4">
              <div className="h-4 bg-muted rounded mb-2"></div>
              <div className="h-6 bg-muted rounded"></div>
            </CardContent>
          </Card>
        ))}
      </div>
    );
  }

  return (
    <div className="grid grid-cols-2 gap-4">
      <Card>
        <CardContent className="p-4">
          <p className="text-sm font-medium text-muted-foreground">Latest {getTypeLabel()}</p>
          <p className="text-2xl font-bold">{formatValue(stats.latest)}</p>
        </CardContent>
      </Card>

      <Card>
        <CardContent className="p-4">
          <p className="text-sm font-medium text-muted-foreground">Average {getTypeLabel()}</p>
          <p className="text-2xl font-bold">{formatValue(stats.average)}</p>
        </CardContent>
      </Card>
    </div>
  );
} 