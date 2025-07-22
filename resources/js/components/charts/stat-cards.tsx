"use client"

import { useEffect, useState } from 'react';
import { Card } from '@/components/ui/card';
import axios from 'axios';
import { bytesToMbps, formatBits } from '@/utils/bitrate';

type StatCardsProps = {
  from?: Date;
  to?: Date;
  server?: string;
  type: 'download' | 'upload' | 'ping';
};

type StatsData = {
  latest: number | null;
  average: number | null;
  // highest and lowest removed
};

export default function StatCards({ from, to, server, type }: StatCardsProps) {
  const [stats, setStats] = useState<StatsData>({
    latest: null,
    average: null,
  });
  const [loading, setLoading] = useState(true);

  useEffect(() => {
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
  }, [from, to, server, type]);

  const formatValue = (value: number | null): string => {
    if (value === null || value === undefined || isNaN(value)) return 'N/A';
    if (type === 'ping') {
      return `${Math.round(value)} ms`;
    }
    const bits = value * 8;
    return formatBits(bits, 0);
  };

  const getTypeLabel = () => {
    switch (type) {
      case 'download': return 'download';
      case 'upload': return 'upload';
      case 'ping': return 'ping';
      default: return type;
    }
  };

  if (loading) {
    return (
      <div className="col-span-1 row-span-2 grid grid-cols-1 gap-2">
        {[1, 2].map((i) => (
          <Card key={i} className="p-3 flex flex-col justify-center text-center animate-pulse h-16" />
        ))}
      </div>
    );
  }

  return (
    <div className="col-span-1 row-span-2 grid grid-cols-1 gap-2">
      <Card className="p-3 flex flex-col justify-center text-center">
        <div className="text-sm font-medium text-muted-foreground">Latest {getTypeLabel()}</div>
        <div className="text-lg font-bold">{formatValue(stats.latest)}</div>
      </Card>
      <Card className="p-3 flex flex-col justify-center text-center">
        <div className="text-sm font-medium text-muted-foreground">Average {getTypeLabel()}</div>
        <div className="text-lg font-bold">{formatValue(stats.average)}</div>
      </Card>
    </div>
  );
} 