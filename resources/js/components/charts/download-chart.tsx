"use client"

import * as React from "react"
import axios from "axios"
import {
  AreaChart,
  Area,
  CartesianGrid,
  XAxis,
  YAxis,
} from "recharts"

import {
  ChartConfig,
  ChartContainer,
  ChartLegend,
  ChartLegendContent,
  ChartTooltip,
  ChartTooltipContent,
} from "@/components/ui/chart"
import { getServerColors } from "@/utils/chart-colors"
import { bytesToMbps } from "@/utils/bitrate";
import { formatDateWithPhpFormat } from "@/utils/date-format";

// Chart config for multiple lines
const chartConfig = {
  download: {
    label: "Download",
    color: "#22c55e",
  },
} satisfies ChartConfig

type DownloadChartProps = {
  from?: Date;
  to?: Date;
  server?: string;
  chartDateTimeFormat?: string;
};
type DownloadData = { 
  created_at: string; 
  download: number | null; 
  server_name?: string;
};

export default function DownloadChart({ from, to, server, chartDateTimeFormat = 'M. j - G:i' }: DownloadChartProps) {
  const [data, setData] = React.useState<DownloadData[]>([]);

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
      .then((res) => setData(res.data));
  }, [from, to, server]);

  // Check if we have multiple servers
  const isMultipleServers = server && server !== 'all' && server.includes(',');
  const selectedServers = isMultipleServers ? server.split(',').map(s => s.trim()) : [];

  // Create dynamic chart config for multiple servers
  const multiServerChartConfig = selectedServers.reduce((config, serverName, index) => {
    config[serverName] = {
      label: serverName,
      color: getServerColors(selectedServers)[index],
    };
    return config;
  }, {} as ChartConfig);

  if (isMultipleServers) {
    // Group data by server and time
    const groupedData: { [key: string]: { [server: string]: number | null } } = {};
    
    data.forEach((d) => {
      const time = d.created_at; // Store original date string
      const serverName = d.server_name || 'Unknown';
      
      if (!groupedData[time]) {
        groupedData[time] = {};
      }
      
      // Use helper function to convert bytes to Mbps and handle null values
      groupedData[time][serverName] = bytesToMbps(d.download);
    });

    const chartData = Object.keys(groupedData).map(time => ({
      time,
      ...groupedData[time]
    })) as Array<{ time: string; [key: string]: number | null | string }>;

    return (
      <ChartContainer config={multiServerChartConfig} className="h-[160px] w-full">
        <AreaChart
          accessibilityLayer
          data={chartData}
          margin={{
            left: -20,
            right: 12,
          }}
        >
          <CartesianGrid vertical={false} />
          <XAxis
            dataKey="time"
            tickLine={false}
            axisLine={false}
            tickMargin={8}
            minTickGap={40}
            tickFormatter={(value) => {
              const date = new Date(value)
              return formatDateWithPhpFormat(date, chartDateTimeFormat)
            }}
          />
          <YAxis
            tickLine={false}
            axisLine={false}
            tickMargin={8}
            tickCount={3}
            tickFormatter={(value) => value.toFixed(0)}
            domain={['dataMin', 'auto']}
          />
          <ChartTooltip
            cursor={false}
            content={
              <ChartTooltipContent
                labelFormatter={(value) => {
                  return formatDateWithPhpFormat(new Date(value), chartDateTimeFormat)
                }}
                indicator="dot"
              />
            }
          />
          <ChartLegend content={<ChartLegendContent />} />
          <defs>
            {selectedServers.map((serverName, index) => (
              <linearGradient key={`gradient-${serverName}`} id={`fill${serverName.replace(/\s+/g, '')}`} x1="0" y1="0" x2="0" y2="1">
                <stop
                  offset="5%"
                  stopColor={getServerColors(selectedServers)[index]}
                  stopOpacity={0.8}
                />
                <stop
                  offset="95%"
                  stopColor={getServerColors(selectedServers)[index]}
                  stopOpacity={0.1}
                />
              </linearGradient>
            ))}
          </defs>
          {selectedServers.map((serverName, index) => {
            // Count how many data points this server has
            const serverDataPoints = chartData.filter(d => d[serverName] !== null && d[serverName] !== undefined).length;
            const showDots = serverDataPoints < 5; // Show dots if less than 5 data points
            
            return (
              <Area
                key={serverName}
                dataKey={serverName}
                type="natural"
                fill={`url(#fill${serverName.replace(/\s+/g, '')})`}
                fillOpacity={0.4}
                stroke={getServerColors(selectedServers)[index]}
                strokeWidth={2}
                stackId="a"
                name={serverName}
                dot={showDots ? { fill: getServerColors(selectedServers)[index], strokeWidth: 2, r: 3 } : false}
              />
            );
          })}
        </AreaChart>
      </ChartContainer>
    );
  } else {
    // Single server or "all" - use area chart as before
    const chartData = data.map((d) => ({
      download: bytesToMbps(d.download),
      time: d.created_at, // Store original date string
    }));

    return (
      <ChartContainer config={chartConfig} className="h-[160px] w-full">
        <AreaChart
          accessibilityLayer
          data={chartData}
          margin={{
            left: -20,
            right: 12,
          }}
        >
          <CartesianGrid vertical={false} />
          <XAxis
            dataKey="time"
            tickLine={false}
            axisLine={false}
            tickMargin={8}
            minTickGap={40}
            tickFormatter={(value) => {
              const date = new Date(value)
              return formatDateWithPhpFormat(date, chartDateTimeFormat)
            }}
          />
          <YAxis
            tickLine={false}
            axisLine={false}
            tickMargin={8}
            tickCount={3}
            tickFormatter={(value) => value.toFixed(0)}
            domain={['dataMin', 'auto']}
          />
          <ChartTooltip
            cursor={false}
            content={
              <ChartTooltipContent
                labelFormatter={(value) => {
                  return formatDateWithPhpFormat(new Date(value), chartDateTimeFormat)
                }}
                indicator="dot"
              />
            }
          />
          <defs>
            <linearGradient id="downloadGradient" x1="0" y1="0" x2="0" y2="1">
              <stop offset="5%" stopColor="#22c55e" stopOpacity={0.8} />
              <stop offset="95%" stopColor="#22c55e" stopOpacity={0.1} />
            </linearGradient>
          </defs>
          <Area
            dataKey="download"
            type="natural"
            fill="url(#downloadGradient)"
            fillOpacity={0.4}
            stroke="#22c55e"
            strokeWidth={2}
            stackId="a"
            name="Download"
          />
        </AreaChart>
      </ChartContainer>
    );
  }
}