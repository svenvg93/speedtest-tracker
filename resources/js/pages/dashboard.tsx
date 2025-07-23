import { AppSidebar } from "@/components/app-sidebar"
import {
  Breadcrumb,
  BreadcrumbItem,
  BreadcrumbLink,
  BreadcrumbList,
  BreadcrumbPage,
  BreadcrumbSeparator,
} from "@/components/ui/breadcrumb"
import { Separator } from "@/components/ui/separator"
import {
  SidebarInset,
  SidebarProvider,
  SidebarTrigger,
} from "@/components/ui/sidebar"
import { Head, usePage } from '@inertiajs/react';
import DownloadChart from '@/components/charts/download-chart';
import UploadChart from '@/components/charts/upload-chart';
import PingChart from '@/components/charts/ping-chart';
import HealthChart from '@/components/charts/health-chart';
import StatusChart from '@/components/charts/status-chart';
import StatCards from '@/components/charts/stat-cards';
import { Button } from "@/components/ui/button"
import {
  DropdownMenu,
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"
import { ChevronDown } from 'lucide-react';
import { Card } from '@/components/ui/card';
import React from "react";
import { subDays } from "date-fns";
import { CalendarRangePopover } from "@/components/ui/calendar-range-popover";
import { ChartContainer } from "@/components/ui/chart";

export default function DashboardPage() {
  const { latest, servers, chartDateTimeFormat } = usePage().props as any;
  const [range, setRange] = React.useState<{ from: Date | undefined; to: Date | undefined }>(() => ({
    from: subDays(new Date(), 7),
    to: new Date(),
  }));
  const [selectedServers, setSelectedServers] = React.useState<string[]>(['all']);

  // Placeholder stats for demonstration
  const stats = [
    { label: "Latest test", value: latest?.healthy ? "Healthy" : "Unhealthy", description: "", badge: { icon: null, text: "", up: undefined }, footer: [] },
    { label: "Data used", value: "420 GB", description: "", badge: { icon: null, text: "", up: undefined }, footer: [] },
  ];

  return (
    <>
      <Head title="Dashboard" />
      <SidebarProvider>
        <AppSidebar />
        <SidebarInset>
          <header className="flex h-16 shrink-0 items-center gap-2 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12">
            <div className="flex items-center gap-2 px-4">
              <SidebarTrigger className="-ml-1" />
              <Separator
                orientation="vertical"
                className="mr-2 data-[orientation=vertical]:h-4"
              />
              <Breadcrumb>
                <BreadcrumbList>
                  <BreadcrumbItem className="hidden md:block">
                    <BreadcrumbLink href="#">
                      Dashboard
                    </BreadcrumbLink>
                  </BreadcrumbItem>
                </BreadcrumbList>
              </Breadcrumb>
            </div>
          </header>
          <div className="flex flex-col gap-4 p-4 pt-0">
            {/* Filters */}
            <div className="flex flex-row flex-wrap items-end gap-2 mb-2">
              <div className="min-w-[420px] w-auto">
                <CalendarRangePopover range={range} setRange={setRange} />
              </div>
              <div className="min-w-[200px] w-auto">
                <DropdownMenu>
                  <DropdownMenuTrigger asChild>
                    <Button variant="outline" className="w-full justify-between">
                      {selectedServers.includes('all') 
                        ? 'All servers' 
                        : selectedServers.length === 1 
                          ? selectedServers[0]
                          : `${selectedServers.length} servers selected`
                      }
                      <ChevronDown className="h-4 w-4" />
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent className="w-56">
                    <DropdownMenuLabel>Select Servers</DropdownMenuLabel>
                    <DropdownMenuSeparator />
                    <DropdownMenuCheckboxItem
                      checked={selectedServers.includes('all')}
                      onCheckedChange={(checked) => {
                        if (checked) {
                          setSelectedServers(['all']);
                        } else {
                          setSelectedServers([]);
                        }
                      }}
                    >
                      All servers
                    </DropdownMenuCheckboxItem>
                    <DropdownMenuSeparator />
                    {servers && servers.filter((name: string) => !!name).map((name: string) => (
                      <DropdownMenuCheckboxItem
                        key={name}
                        checked={selectedServers.includes(name)}
                        onCheckedChange={(checked) => {
                          if (checked) {
                            // Remove 'all' if it's selected and add this server
                            const newServers = selectedServers.includes('all') 
                              ? [name]
                              : [...selectedServers.filter(s => s !== 'all'), name];
                            setSelectedServers(newServers);
                          } else {
                            // Remove this server
                            const newServers = selectedServers.filter(s => s !== name);
                            setSelectedServers(newServers.length > 0 ? newServers : ['all']);
                          }
                        }}
                      >
                        {name}
                      </DropdownMenuCheckboxItem>
                    ))}
                  </DropdownMenuContent>
                </DropdownMenu>
              </div>
            </div>
            {/* Grid Layout */}
            <div className="grid gap-4 grid-cols-1 md:grid-cols-4 auto-rows-min">
              {/* Top row: stat cards and progress */}
              <Card className="col-span-2 row-span-1 flex flex-col justify-center p-4">
                <div className="font-medium mb-2">Test Health</div>
                <HealthChart from={range.from} to={range.to} server={selectedServers.includes('all') ? 'all' : selectedServers.join(',')} />
              </Card>
              <Card className="col-span-2 row-span-1 flex flex-col justify-center p-4">
                <div className="font-medium mb-2">Test Status</div>
                <StatusChart from={range.from} to={range.to} server={selectedServers.includes('all') ? 'all' : selectedServers.join(',')} />
              </Card>

              {/* Download over time chart */}
              <Card className="col-span-3 row-span-2 p-4 h-[240px] flex flex-col">
                <div className="font-medium mb-2">Download (Mbps)</div>
                <ChartContainer config={{ download: { color: '#22c55e', label: 'Download' } }} className="flex-1">
                  <DownloadChart from={range.from} to={range.to} server={selectedServers.includes('all') ? 'all' : selectedServers.join(',')} chartDateTimeFormat={chartDateTimeFormat} />
                </ChartContainer>
              </Card>

              {/* Download Statistics */}
              <StatCards 
                from={range.from} 
                to={range.to} 
                server={selectedServers.includes('all') ? 'all' : selectedServers.join(',')} 
                type="download" 
              />

              {/* Upload Statistics */}
              <StatCards 
                from={range.from} 
                to={range.to} 
                server={selectedServers.includes('all') ? 'all' : selectedServers.join(',')} 
                type="upload" 
              />

              {/* Upload over time chart */}
              <Card className="col-span-3 row-span-2 p-4 h-[240px] flex flex-col">
                <div className="font-medium mb-2">Upload (Mbps)</div>
                <ChartContainer config={{ upload: { color: '#2563eb', label: 'Upload' } }} className="flex-1">
                  <UploadChart from={range.from} to={range.to} server={selectedServers.includes('all') ? 'all' : selectedServers.join(',')} chartDateTimeFormat={chartDateTimeFormat} />
                </ChartContainer>
              </Card>

              {/* Ping over time chart */}
              <Card className="col-span-3 row-span-2 p-4 h-[240px] flex flex-col">
                <div className="font-medium mb-2">Ping (Ms)</div>
                <ChartContainer config={{ ping: { color: '#f59e42', label: 'Ping' } }} className="flex-1">
                  <PingChart from={range.from} to={range.to} server={selectedServers.includes('all') ? 'all' : selectedServers.join(',')} chartDateTimeFormat={chartDateTimeFormat} />
                </ChartContainer>
              </Card>

              {/* Ping Statistics */}
              <StatCards 
                from={range.from} 
                to={range.to} 
                server={selectedServers.includes('all') ? 'all' : selectedServers.join(',')} 
                type="ping" 
              />
            </div>
          </div>
        </SidebarInset>
      </SidebarProvider>
    </>
  );
}
