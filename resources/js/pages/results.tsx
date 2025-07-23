import * as React from "react";
import { Head, usePage } from '@inertiajs/react';
import { Link as InertiaLink, usePage as useInertiaPage } from '@inertiajs/react';
import {
  ColumnDef,
  flexRender,
  getCoreRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useReactTable,
  SortingState,
  ColumnFiltersState,
  VisibilityState,
  OnChangeFn,
} from "@tanstack/react-table";
import { ArrowUpDown, ChevronDown, MoreHorizontal, Link as LinkIcon, Eye, MessageCircle, Trash, CheckCircle, XCircle, Loader2, ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import {
  IconChevronLeft,
  IconChevronRight,
  IconChevronsLeft,
  IconChevronsRight,
} from "@tabler/icons-react"
import {
  DropdownMenu,
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { Badge } from "@/components/ui/badge";
import AppLayout from '@/layouts/app-layout';
import { ResultStatus } from "@/enums/ResultStatus";
import { Checkbox } from "@/components/ui/checkbox";
import { Label } from "@/components/ui/label";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { router } from '@inertiajs/react';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogClose } from "@/components/ui/dialog";
import ResultDetailsChart, { Result as ResultType } from "@/components/ResultDetailsChart";


export type Result = ResultType;





export default function ResultsPage() {
  const { results } = (usePage().props as unknown) as { results: { data: Result[], [key: string]: any } };

  // Initialize column visibility from URL parameters or default values
  const getInitialColumnVisibility = (): VisibilityState => {
    if (typeof window === 'undefined') {
      // Server-side rendering fallback
      return {
        'data.interface.externalIp': false,
        'data.isp': false,
        'service': false,
        'data.server.location': false,
        'data.download.latency.jitter': false,
        'data.download.latency.high': false,
        'data.download.latency.low': false,
        'data.download.latency.iqm': false,
        'data.upload.latency.jitter': false,
        'data.upload.latency.high': false,
        'data.upload.latency.low': false,
        'data.upload.latency.iqm': false,
        'data.ping.low': false,
        'data.ping.jitter': false,
        'data.ping.high': false,
        'data.download.bytes': false,
        'data.upload.bytes': false,
        'data.download.elapsed': false,
        'data.upload.elapsed': false,
        'data.packetLoss': false,
        'scheduled': false,
        'healthy': false,
        'comments': false,
        'data.message': false,
        'updated_at': false,
      };
    }

    const urlParams = new URLSearchParams(window.location.search);
    const columnsParam = urlParams.get('columns');
    
    if (columnsParam) {
      try {
        const visibleColumns = columnsParam.split(',');
        const defaultVisibility: VisibilityState = {
          'data.interface.externalIp': false,
          'data.isp': false,
          'service': false,
          'data.server.location': false,
          'data.download.latency.jitter': false,
          'data.download.latency.high': false,
          'data.download.latency.low': false,
          'data.download.latency.iqm': false,
          'data.upload.latency.jitter': false,
          'data.upload.latency.high': false,
          'data.upload.latency.low': false,
          'data.upload.latency.iqm': false,
          'data.ping.low': false,
          'data.ping.jitter': false,
          'data.ping.high': false,
          'data.download.bytes': false,
          'data.upload.bytes': false,
          'data.download.elapsed': false,
          'data.upload.elapsed': false,
          'data.packetLoss': false,
          'scheduled': false,
          'healthy': false,
          'comments': false,
          'data.message': false,
          'updated_at': false,
        };

        // Set visible columns to true based on URL parameter
        visibleColumns.forEach(column => {
          if (column in defaultVisibility) {
            defaultVisibility[column] = true;
          }
        });

        return defaultVisibility;
      } catch (error) {
        console.error('Error parsing columns parameter:', error);
      }
    }

    // Return default visibility if no URL parameter or parsing failed
    return {
      'data.interface.externalIp': false,
      'data.isp': false,
      'service': false,
      'data.server.location': false,
      'data.download.latency.jitter': false,
      'data.download.latency.high': false,
      'data.download.latency.low': false,
      'data.download.latency.iqm': false,
      'data.upload.latency.jitter': false,
      'data.upload.latency.high': false,
      'data.upload.latency.low': false,
      'data.upload.latency.iqm': false,
      'data.ping.low': false,
      'data.ping.jitter': false,
      'data.ping.high': false,
      'data.download.bytes': false,
      'data.upload.bytes': false,
      'data.download.elapsed': false,
      'data.upload.elapsed': false,
      'data.packetLoss': false,
      'scheduled': false,
      'healthy': false,
      'comments': false,
      'data.message': false,
      'updated_at': false,
    };
  };

  const [sorting, setSorting] = React.useState<SortingState>([]);
  const [columnFilters, setColumnFilters] = React.useState<ColumnFiltersState>([]);
  const [columnVisibility, setColumnVisibility] = React.useState<VisibilityState>(getInitialColumnVisibility);
  const [rowSelection, setRowSelection] = React.useState({});
  const [showComments, setShowComments] = React.useState(false);
  const [selectedResult, setSelectedResult] = React.useState<Result | null>(null);
  const [comments, setComments] = React.useState("");
  const [showResultDetails, setShowResultDetails] = React.useState(false);
  const [isDeleteDialogOpen, setIsDeleteDialogOpen] = React.useState(false);
  const [selectedResultForDelete, setSelectedResultForDelete] = React.useState<Result | null>(null);
  const [pagination, setPagination] = React.useState({
    pageIndex: 0,
    pageSize: 10,
  });

  // Update URL when column visibility changes
  const updateColumnVisibility: OnChangeFn<VisibilityState> = React.useCallback((updaterOrValue) => {
    const newVisibility = typeof updaterOrValue === 'function' ? updaterOrValue(columnVisibility) : updaterOrValue;
    setColumnVisibility(newVisibility);
    
    if (typeof window !== 'undefined') {
      const urlParams = new URLSearchParams(window.location.search);
      const visibleColumns = Object.entries(newVisibility)
        .filter(([_, isVisible]) => isVisible)
        .map(([column]) => column);
      
      if (visibleColumns.length > 0) {
        urlParams.set('columns', visibleColumns.join(','));
      } else {
        urlParams.delete('columns');
      }
      
      const newUrl = `${window.location.pathname}?${urlParams.toString()}`;
      window.history.replaceState({}, '', newUrl);
    }
  }, [columnVisibility]);

  const columns: ColumnDef<Result>[] = [
    {
      id: "select",
      header: ({ table }) => (
        <Checkbox
          checked={table.getIsAllPageRowsSelected()}
          onCheckedChange={value => table.toggleAllPageRowsSelected(!!value)}
          aria-label="Select all"
        />
      ),
      cell: ({ row }) => (
        <Checkbox
          checked={row.getIsSelected()}
          onCheckedChange={value => row.toggleSelected(!!value)}
          aria-label="Select row"
        />
      ),
      enableSorting: false,
      enableHiding: false,
    },
    {
      accessorKey: "id",
      header: "ID",
      cell: ({ row }) => row.getValue("id"),
    },
    {
      accessorKey: "service",
      id: 'service',
      header: "Service",
      cell: ({ row }) => (
        <div className="w-32">
          <Badge variant="outline" className="text-muted-foreground px-1.5 text-sm">
            {row.getValue("service")}
          </Badge>
        </div>
      ),
    },
    {
        accessorKey: 'data.server.name',
        id: 'data.server.name',
        header: 'Server Name',
        cell: ({ row }) => row.original.data?.server?.name ?? ''
    },
    {
      accessorKey: "download",
      header: "Download",
      cell: ({ row }) => {
        const download = row.getValue("download");
        if (download === null || download === undefined || isNaN(Number(download))) return '';
        return `${((Number(download) * 8) / 1_000_000).toFixed(2)} Mbit`;
      },
    },
    {
      accessorKey: "upload",
      header: "Upload",
      cell: ({ row }) => {
        const upload = row.getValue("upload");
        if (upload === null || upload === undefined || isNaN(Number(upload))) return '';
        return `${((Number(upload) * 8) / 1_000_000).toFixed(2)} Mbit`;
      },
    },
    {
      accessorKey: "ping",
      header: "Ping (ms)",
      cell: ({ row }) => {
        const ping = row.getValue("ping");
        if (ping === null || ping === undefined || isNaN(Number(ping))) return '';
        return `${Math.round(Number(ping))} ms`;
      },
    },
    {
      accessorKey: "status",
      header: "Status",
      cell: ({ row }) => {
        const status = row.getValue("status") as string;
        return (
          <Badge variant="outline" className="text-muted-foreground px-1.5 text-sm">
            {status === ResultStatus.Completed ? (
              <CheckCircle className="fill-green-500 dark:fill-green-400 mr-1 h-4 w-4" />
            ) : status === ResultStatus.Failed ? (
              <XCircle className="fill-red-500 dark:fill-red-400 mr-1 h-4 w-4" />
            ) : (
              <Loader2 className="mr-1 h-4 w-4 animate-spin" />
            )}
            {status}
          </Badge>
        );
      },
    },
    {
      accessorKey: "created_at",
      header: "Created At",
      cell: ({ row }) => new Date(row.getValue("created_at")).toLocaleString(),
    },
    {
      accessorKey: 'data.isp',
      id: 'data.isp',
      header: 'ISP',
      cell: ({ row }) => row.original.data?.isp ?? ''
    },
    {
      accessorKey: 'data.interface.externalIp',
      id: 'data.interface.externalIp',
      header: 'External IP',
      cell: ({ row }) => row.original.data?.interface?.externalIp ?? ''
    },
    {
      accessorKey: 'data.server.location',
      id: 'data.server.location',
      header: 'Server Location',
      cell: ({ row }) => row.original.data?.server?.location ?? ''
    },
    {
      accessorKey: 'data.download.latency.jitter',
      id: 'data.download.latency.jitter',
      header: 'Download Jitter',
      cell: ({ row }) => {
        const jitter = row.original.data?.download?.latency?.jitter;
        if (jitter === null || jitter === undefined || isNaN(Number(jitter))) return '';
        return `${Math.round(Number(jitter))} ms`;
      }
    },
    {
      accessorKey: 'data.download.latency.high',
      id: 'data.download.latency.high',
      header: 'Download Latency High',
      cell: ({ row }) => {
        const high = row.original.data?.download?.latency?.high;
        if (high === null || high === undefined || isNaN(Number(high))) return '';
        return `${Math.round(Number(high))} ms`;
      }
    },
    {
      accessorKey: 'data.download.latency.low',
      id: 'data.download.latency.low',
      header: 'Download Latency Low',
      cell: ({ row }) => {
        const low = row.original.data?.download?.latency?.low;
        if (low === null || low === undefined || isNaN(Number(low))) return '';
        return `${Math.round(Number(low))} ms`;
      }
    },
    {
      accessorKey: 'data.download.latency.iqm',
      id: 'data.download.latency.iqm',
      header: 'Download Latency IQM',
      cell: ({ row }) => {
        const iqm = row.original.data?.download?.latency?.iqm;
        if (iqm === null || iqm === undefined || isNaN(Number(iqm))) return '';
        return `${Math.round(Number(iqm))} ms`;
      }
    },
    {
      accessorKey: 'data.upload.latency.jitter',
      id: 'data.upload.latency.jitter',
      header: 'Upload Jitter',
      cell: ({ row }) => {
        const jitter = row.original.data?.upload?.latency?.jitter;
        if (jitter === null || jitter === undefined || isNaN(Number(jitter))) return '';
        return `${Math.round(Number(jitter))} ms`;
      }
    },
    {
      accessorKey: 'data.upload.latency.high',
      id: 'data.upload.latency.high',
      header: 'Upload Latency High',
      cell: ({ row }) => {
        const high = row.original.data?.upload?.latency?.high;
        if (high === null || high === undefined || isNaN(Number(high))) return '';
        return `${Math.round(Number(high))} ms`;
      }
    },
    {
      accessorKey: 'data.upload.latency.low',
      id: 'data.upload.latency.low',
      header: 'Upload Latency Low',
      cell: ({ row }) => {
        const low = row.original.data?.upload?.latency?.low;
        if (low === null || low === undefined || isNaN(Number(low))) return '';
        return `${Math.round(Number(low))} ms`;
      }
    },
    {
      accessorKey: 'data.upload.latency.iqm',
      id: 'data.upload.latency.iqm',
      header: 'Upload Latency IQM',
      cell: ({ row }) => {
        const iqm = row.original.data?.upload?.latency?.iqm;
        if (iqm === null || iqm === undefined || isNaN(Number(iqm))) return '';
        return `${Math.round(Number(iqm))} ms`;
      }
    },
    {
      accessorKey: 'data.ping.jitter',
      id: 'data.ping.jitter',
      header: 'Ping Jitter',
      cell: ({ row }) => {
        const jitter = row.original.data?.ping?.jitter;
        if (jitter === null || jitter === undefined || isNaN(Number(jitter))) return '';
        return `${Math.round(Number(jitter))} ms`;
      }
    },
    {
      accessorKey: 'data.ping.low',
      id: 'data.ping.low',
      header: 'Ping Low',
      cell: ({ row }) => {
        const low = row.original.data?.ping?.low;
        if (low === null || low === undefined || isNaN(Number(low))) return '';
        return `${Math.round(Number(low))} ms`;
      }
    },
    {
      accessorKey: 'data.ping.high',
      id: 'data.ping.high',
      header: 'Ping High',
      cell: ({ row }) => {
        const high = row.original.data?.ping?.high;
        if (high === null || high === undefined || isNaN(Number(high))) return '';
        return `${Math.round(Number(high))} ms`;
      }
    },
    {
      accessorKey: 'data.packetLoss',
      id: 'data.packetLoss',
      header: 'Packet Loss',
      cell: ({ row }) => row.original.data?.packetLoss ?? ''
    },
    {
      accessorKey: 'data.download.bytes',
      id: 'data.download.bytes',
      header: 'Download Bytes',
      cell: ({ row }) => {
        const bytes = row.original.data?.download?.bytes;
        if (bytes === null || bytes === undefined || isNaN(Number(bytes))) return '';
        return `${(Number(bytes) / (1024 * 1024)).toFixed(0)} MB`;
      }
    },
    {
      accessorKey: 'data.upload.bytes',
      id: 'data.upload.bytes',
      header: 'Upload Bytes',
      cell: ({ row }) => {
        const bytes = row.original.data?.upload?.bytes;
        if (bytes === null || bytes === undefined || isNaN(Number(bytes))) return '';
        return `${(Number(bytes) / (1024 * 1024)).toFixed(0)} MB`;
      }
    },
    {
      accessorKey: 'data.download.elapsed',
      id: 'data.download.elapsed',
      header: 'Download Elapsed',
      cell: ({ row }) => {
        const elapsed = row.original.data?.download?.elapsed;
        if (elapsed === null || elapsed === undefined || isNaN(Number(elapsed))) return '';
        return `${(Number(elapsed) / 1000).toFixed(0)} s`;
      }
    },
    {
      accessorKey: 'data.upload.elapsed',
      id: 'data.upload.elapsed',
      header: 'Upload Elapsed',
      cell: ({ row }) => {
        const elapsed = row.original.data?.upload?.elapsed;
        if (elapsed === null || elapsed === undefined || isNaN(Number(elapsed))) return '';
        return `${(Number(elapsed) / 1000).toFixed(0)} s`;
      }
    },
    {
      accessorKey: 'data.message',
      id: 'data.message',
      header: 'Message',
      cell: ({ row }) => row.original.data?.message ?? ''
    },
    {
      accessorKey: 'scheduled',
      id: 'scheduled',
      header: 'Scheduled',
      cell: ({ row }) => (
        <Badge variant="outline" className="text-muted-foreground px-1.5 text-sm">
          {row.original.scheduled ? (
            <CheckCircle className="fill-green-500 dark:fill-green-400 mr-1 h-4 w-4" />
          ) : (
            <XCircle className="fill-red-500 dark:fill-red-400 mr-1 h-4 w-4" />
          )}
          {row.original.scheduled ? 'Yes' : 'No'}
        </Badge>
      ),
    },
    {
      accessorKey: 'healthy',
      id: 'healthy',
      header: 'Healthy',
      cell: ({ row }) => (
        <Badge variant="outline" className="text-muted-foreground px-1.5 text-sm">
          {row.original.healthy ? (
            <CheckCircle className="fill-green-500 dark:fill-green-400 mr-1 h-4 w-4" />
          ) : (
            <XCircle className="fill-red-500 dark:fill-red-400 mr-1 h-4 w-4" />
          )}
          {row.original.healthy ? 'Yes' : 'No'}
        </Badge>
      ),
    },
    {
      accessorKey: 'comments',
      id: 'comments',
      header: 'Comments',
      cell: ({ row }) => row.original.comments ?? ''
    },
    {
      accessorKey: 'updated_at',
      id: 'updated_at',
      header: 'Updated At',
      cell: ({ row }) => row.original.updated_at ? new Date(row.original.updated_at).toLocaleString() : '',
    },
    {
      id: "actions",
      enableHiding: false,
      cell: ({ row }) => {
        const result = row.original;
        const user = (usePage().props as any)?.auth?.user || {};
        const handleDelete = (e: React.MouseEvent) => {
          e.preventDefault();
          e.stopPropagation();
          setSelectedResultForDelete(result);
          setIsDeleteDialogOpen(true);
        }
        return (
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="ghost" className="h-8 w-8 p-0">
                <span className="sr-only">Open menu</span>
                <MoreHorizontal />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              <DropdownMenuLabel>Actions</DropdownMenuLabel>
              {result.status === ResultStatus.Completed && result.result_url && (
                <DropdownMenuItem asChild>
                  <a href={result.result_url} target="_blank" rel="noopener noreferrer" className="flex items-center">
                    <LinkIcon className="mr-2 h-4 w-4" />
                    View on Speedtest.net
                  </a>
                </DropdownMenuItem>
              )}
              <DropdownMenuItem
                onClick={() => {
                  setSelectedResult(result);
                  setShowResultDetails(true);
                }}
                className="flex items-center"
              >
                <Eye className="mr-2 h-4 w-4" />
                View details
              </DropdownMenuItem>
              {(user.is_admin || user.is_user) && (
                <DropdownMenuItem
                  onClick={() => {
                    setSelectedResult(result);
                    setComments(result.comments || "");
                    setShowComments(true);
                  }}
                  className="flex items-center"
                >
                  <MessageCircle className="mr-2 h-4 w-4" />
                  Update Comments
                </DropdownMenuItem>
              )}
              <DropdownMenuItem 
                key={`delete-${result.id}`}
                onClick={handleDelete} 
                className="flex items-center text-red-600"
                onSelect={(e) => e.preventDefault()}
              >
                <Trash className="mr-2 h-4 w-4" />
                Delete
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        );
      },
    },
  ];

  const table = useReactTable({
    data: results.data,
    columns,
    onSortingChange: setSorting,
    onColumnFiltersChange: setColumnFilters,
    onPaginationChange: setPagination,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    onColumnVisibilityChange: updateColumnVisibility,
    onRowSelectionChange: setRowSelection,
    state: {
      sorting,
      columnFilters,
      columnVisibility,
      rowSelection,
      pagination,
    },
    getRowId: (row) => row.id.toString(),
  });



  function handleDeleteSelected() {
    const selectedIds = table.getSelectedRowModel().rows.map(row => row.original.id);
    if (selectedIds.length === 0) return;
    if (!window.confirm(`Delete ${selectedIds.length} selected records?`)) return;
    router.post('/results/delete', { ids: selectedIds, _method: 'delete' });
  }

  const handleDeleteResult = () => {
    if (!selectedResultForDelete) return;
    
    router.post(`/results/${selectedResultForDelete.id}`, { _method: 'delete' }, {
      onSuccess: () => {
        setIsDeleteDialogOpen(false);
        setSelectedResultForDelete(null);
      }
    });
  };

  return (
    <AppLayout breadcrumbs={[{ title: 'Results', href: '/results' }]}> 
      <Head title="Results" />
      <div className="w-full p-6">
        <div className="flex items-center py-4">
          <Input
            placeholder="Filter status..."
            value={(table.getColumn("status")?.getFilterValue() as string) ?? ""}
            onChange={(event) => table.getColumn("status")?.setFilterValue(event.target.value)}
            className="max-w-sm"
          />
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="outline" className="ml-auto">
                Columns <ChevronDown />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" className="max-h-140 overflow-y-auto">
              {table.getAllColumns().filter((column) => column.getCanHide()).map((column) => (
                <DropdownMenuCheckboxItem
                  key={column.id}
                  className="capitalize"
                  checked={column.getIsVisible()}
                  onCheckedChange={(value) => column.toggleVisibility(!!value)}
                >
                  {typeof column.columnDef.header === 'string'
                    ? column.columnDef.header
                    : column.id}
                </DropdownMenuCheckboxItem>
              ))}
            </DropdownMenuContent>
          </DropdownMenu>
        </div>
        <div className="rounded-md border">
          <Table>
            <TableHeader>
              {table.getHeaderGroups().map((headerGroup) => (
                <TableRow key={headerGroup.id}>
                  {headerGroup.headers.map((header) => (
                    <TableHead key={header.id}>
                      {header.isPlaceholder ? null : flexRender(header.column.columnDef.header, header.getContext())}
                    </TableHead>
                  ))}
                </TableRow>
              ))}
            </TableHeader>
            <TableBody>
              {table.getRowModel().rows?.length ? (
                table.getRowModel().rows.map((row) => (
                  <TableRow 
                    key={row.id} 
                    data-state={row.getIsSelected() && "selected"}
                    className="cursor-pointer hover:bg-muted/50"
                    onClick={() => {
                      setSelectedResult(row.original);
                      setShowResultDetails(true);
                    }}
                  >
                    {row.getVisibleCells().map((cell) => (
                      <TableCell key={cell.id}>
                        {flexRender(cell.column.columnDef.cell, cell.getContext())}
                      </TableCell>
                    ))}
                  </TableRow>
                ))
              ) : (
                <TableRow>
                  <TableCell colSpan={columns.length} className="h-24 text-center">
                    No results.
                  </TableCell>
                </TableRow>
              )}
            </TableBody>
          </Table>
        </div>
        <div className="flex items-center justify-between px-4 mt-6">
          <div className="text-muted-foreground hidden flex-1 text-sm lg:flex">
            {table.getFilteredSelectedRowModel().rows.length} of{" "}
            {table.getFilteredRowModel().rows.length} row(s) selected.
          </div>
          <div className="flex w-full items-center gap-8 lg:w-fit">
            <div className="hidden items-center gap-2 lg:flex">
              <Label htmlFor="rows-per-page" className="text-sm font-medium">
                Rows per page
              </Label>
              <Select
                value={`${table.getState().pagination.pageSize}`}
                onValueChange={(value) => {
                  table.setPageSize(Number(value))
                }}
              >
                <SelectTrigger className="w-20" id="rows-per-page">
                  <SelectValue
                    placeholder={table.getState().pagination.pageSize}
                  />
                </SelectTrigger>
                <SelectContent side="top">
                  {[10, 20, 30, 40, 50].map((pageSize) => (
                    <SelectItem key={pageSize} value={`${pageSize}`}>
                      {pageSize}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
            <div className="flex w-fit items-center justify-center text-sm font-medium">
              Page {table.getState().pagination.pageIndex + 1} of{" "}
              {table.getPageCount()}
            </div>
            <div className="ml-auto flex items-center gap-2 lg:ml-0">
              <Button
                variant="outline"
                className="hidden h-8 w-8 p-0 lg:flex"
                onClick={() => table.setPageIndex(0)}
                disabled={!table.getCanPreviousPage()}
              >
                <span className="sr-only">Go to first page</span>
                <IconChevronsLeft />
              </Button>
              <Button
                variant="outline"
                className="size-8"
                size="icon"
                onClick={() => table.previousPage()}
                disabled={!table.getCanPreviousPage()}
              >
                <span className="sr-only">Go to previous page</span>
                <IconChevronLeft />
              </Button>
              <Button
                variant="outline"
                className="size-8"
                size="icon"
                onClick={() => table.nextPage()}
                disabled={!table.getCanNextPage()}
              >
                <span className="sr-only">Go to next page</span>
                <IconChevronRight />
              </Button>
              <Button
                variant="outline"
                className="hidden size-8 lg:flex"
                size="icon"
                onClick={() => table.setPageIndex(table.getPageCount() - 1)}
                disabled={!table.getCanNextPage()}
              >
                <span className="sr-only">Go to last page</span>
                <IconChevronsRight />
              </Button>
            </div>
          </div>
        </div>
        <div className="flex items-center justify-between space-x-2 py-4">
          <div className="space-x-2">
            {table.getSelectedRowModel().rows.length > 0 && (
              <Button
                variant="destructive"
                onClick={handleDeleteSelected}
              >
                Delete Selected
              </Button>
            )}
          </div>
        </div>
      </div>
      <Dialog open={showComments} onOpenChange={setShowComments}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Update Comments</DialogTitle>
          </DialogHeader>
          <textarea
            rows={6}
            maxLength={500}
            className="w-full border rounded p-2 mb-4"
            value={comments}
            onChange={e => setComments(e.target.value)}
          />
          <DialogFooter>
            <DialogClose asChild>
              <Button variant="outline">Cancel</Button>
            </DialogClose>
            <Button
              onClick={() => {
                if (selectedResult) {
                  router.post(`/results/${selectedResult.id}/comments`, { comments }, {
                    onSuccess: () => {
                      setShowComments(false);
                      router.visit(window.location.pathname, { only: ['results'], preserveScroll: true });
                    }
                  });
                }
              }}
            >
              Save
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      

      {/* Result Details Chart */}
      <ResultDetailsChart 
        result={selectedResult}
        open={showResultDetails}
        onOpenChange={setShowResultDetails}
      />

      {/* Delete Result Dialog */}
      <Dialog open={isDeleteDialogOpen} onOpenChange={setIsDeleteDialogOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Delete Result</DialogTitle>
          </DialogHeader>
          <p>Are you sure you want to delete this speedtest result? This action cannot be undone.</p>
          <DialogFooter>
            <DialogClose asChild>
              <Button variant="outline">Cancel</Button>
            </DialogClose>
            <Button variant="destructive" onClick={handleDeleteResult}>Delete Result</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </AppLayout>
  );
} 