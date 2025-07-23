import * as React from "react";
import { Head, usePage } from '@inertiajs/react';
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
} from "@tanstack/react-table";
import { ArrowUpDown, ChevronDown, MoreHorizontal, Plus, Edit, Trash, Copy, Eye, EyeOff, CheckCircle, XCircle, Calendar, Clock } from "lucide-react";
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
import { DatePicker } from "@/components/ui/date-picker";
import { formatDistanceToNow, format } from 'date-fns';

export interface ApiToken {
  id: number;
  name: string;
  abilities: string[];
  created_at: string;
  last_used_at: string | null;
  expires_at: string | null;
  is_expired: boolean;
  plain_text_token?: string;
}

export default function ApiTokensPage() {
  const { tokens, newToken, flash } = (usePage().props as unknown) as { 
    tokens: ApiToken[];
    newToken?: {
      id: number;
      name: string;
      plain_text_token: string;
    };
    flash?: {
      success?: string;
      error?: string;
    };
  };

  // State for table
  const [sorting, setSorting] = React.useState<SortingState>([]);
  const [columnFilters, setColumnFilters] = React.useState<ColumnFiltersState>([]);
  const [columnVisibility, setColumnVisibility] = React.useState<VisibilityState>({});
  const [rowSelection, setRowSelection] = React.useState({});

  // State for dialogs
  const [showCreateDialog, setShowCreateDialog] = React.useState(false);
  const [showEditDialog, setShowEditDialog] = React.useState(false);
  const [showNewTokenDialog, setShowNewTokenDialog] = React.useState(false);
  const [selectedToken, setSelectedToken] = React.useState<ApiToken | null>(null);
  const [isDeleteDialogOpen, setIsDeleteDialogOpen] = React.useState(false);
  const [selectedTokenForDelete, setSelectedTokenForDelete] = React.useState<ApiToken | null>(null);
  const [currentNewToken, setCurrentNewToken] = React.useState<{
    id: number;
    name: string;
    plain_text_token: string;
  } | null>(null);

  // Form state
  const [formData, setFormData] = React.useState({
    name: '',
    abilities: [] as string[],
    expires_at: '',
  });

  const abilityOptions = [
    { value: 'results:read', label: 'Read results', description: 'Allow this token to read results.' },
    { value: 'speedtests:run', label: 'Run speedtest', description: 'Allow this token to run speedtests.' },
    { value: 'ookla:list-servers', label: 'List servers', description: 'Allow this token to list servers.' },
  ];

  // Handle new token from backend
  React.useEffect(() => {
    if (newToken) {
      setCurrentNewToken(newToken);
      setShowNewTokenDialog(true);
    }
  }, [newToken]);

  const columns: ColumnDef<ApiToken>[] = [
    {
      id: "select",
      header: ({ table }) => (
        <Checkbox
          checked={
            table.getIsAllPageRowsSelected() ||
            (table.getIsSomePageRowsSelected() && "indeterminate")
          }
          onCheckedChange={(value) => table.toggleAllPageRowsSelected(!!value)}
          aria-label="Select all"
        />
      ),
      cell: ({ row }) => (
        <Checkbox
          checked={row.getIsSelected()}
          onCheckedChange={(value) => row.toggleSelected(!!value)}
          aria-label="Select row"
        />
      ),
      enableSorting: false,
      enableHiding: false,
    },
    {
      accessorKey: "name",
      header: ({ column }) => {
        return (
          <Button
            variant="ghost"
            onClick={() => column.toggleSorting(column.getIsSorted() === "asc")}
          >
            Name
            <ArrowUpDown className="ml-2 h-4 w-4" />
          </Button>
        )
      },
      cell: ({ row }) => <div className="font-medium">{row.getValue("name")}</div>,
    },
    {
      accessorKey: "abilities",
      header: "Abilities",
      cell: ({ row }) => {
        const abilities = row.getValue("abilities") as string[];
        return (
          <div className="flex flex-wrap gap-1">
            {abilities.map((ability) => (
              <Badge key={ability} variant="secondary" className="text-xs">
                {ability}
              </Badge>
            ))}
          </div>
        );
      },
    },
    {
      accessorKey: "created_at",
      header: ({ column }) => {
        return (
          <Button
            variant="ghost"
            onClick={() => column.toggleSorting(column.getIsSorted() === "asc")}
          >
            Created
            <ArrowUpDown className="ml-2 h-4 w-4" />
          </Button>
        )
      },
      cell: ({ row }) => {
        const date = new Date(row.getValue("created_at"));
        return (
          <div className="flex items-center gap-2">
            <Calendar className="h-4 w-4 text-muted-foreground" />
            <span>{format(date, 'MMM dd, yyyy')}</span>
          </div>
        );
      },
    },
    {
      accessorKey: "last_used_at",
      header: "Last Used",
      cell: ({ row }) => {
        const lastUsed = row.getValue("last_used_at") as string | null;
        if (!lastUsed) {
          return <span className="text-muted-foreground">Never</span>;
        }
        const date = new Date(lastUsed);
        return (
          <div className="flex items-center gap-2">
            <Clock className="h-4 w-4 text-muted-foreground" />
            <span>{formatDistanceToNow(date, { addSuffix: true })}</span>
          </div>
        );
      },
    },
    {
      accessorKey: "expires_at",
      header: "Expires",
      cell: ({ row }) => {
        const expiresAt = row.getValue("expires_at") as string | null;
        const isExpired = row.getValue("is_expired") as boolean;
        
        if (!expiresAt) {
          return <span className="text-muted-foreground">Never</span>;
        }
        
        const date = new Date(expiresAt);
        return (
          <div className="flex items-center gap-2">
            {isExpired ? (
              <XCircle className="h-4 w-4 text-destructive" />
            ) : (
              <CheckCircle className="h-4 w-4 text-green-500" />
            )}
            <span className={isExpired ? "text-destructive" : ""}>
              {format(date, 'MMM dd, yyyy')}
            </span>
          </div>
        );
      },
    },
    {
      id: "actions",
      enableHiding: false,
      cell: ({ row }) => {
        const token = row.original;

        return (
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="ghost" className="h-8 w-8 p-0">
                <span className="sr-only">Open menu</span>
                <MoreHorizontal className="h-4 w-4" />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              <DropdownMenuLabel>Actions</DropdownMenuLabel>
              <DropdownMenuItem
                onClick={() => {
                  setSelectedToken(token);
                  setFormData({
                    name: token.name,
                    abilities: token.abilities,
                    expires_at: token.expires_at ? format(new Date(token.expires_at), "yyyy-MM-dd'T'HH:mm") : '',
                  });
                  setShowEditDialog(true);
                }}
                disabled={token.is_expired}
              >
                <Edit className="mr-2 h-4 w-4" />
                Edit
              </DropdownMenuItem>
              <DropdownMenuItem
                onClick={() => {
                  setSelectedTokenForDelete(token);
                  setIsDeleteDialogOpen(true);
                }}
                className="text-destructive"
              >
                <Trash className="mr-2 h-4 w-4" />
                Delete
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        )
      },
    },
  ];

  const table = useReactTable({
    data: tokens,
    columns,
    onSortingChange: setSorting,
    onColumnFiltersChange: setColumnFilters,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    onColumnVisibilityChange: setColumnVisibility,
    onRowSelectionChange: setRowSelection,
    state: {
      sorting,
      columnFilters,
      columnVisibility,
      rowSelection,
    },
  });

  const handleCreateToken = () => {
    // Prepare the data, converting empty string to null for expires_at
    const requestData = {
      ...formData,
      expires_at: formData.expires_at || null
    };

    router.post('/api-tokens', requestData, {
      onSuccess: (page) => {
        // Handle success - we'll need to get the token from the response
        // For now, just close the dialog and refresh
        setShowCreateDialog(false);
        setFormData({ name: '', abilities: [], expires_at: '' });
        router.visit(window.location.pathname, { only: ['tokens'], preserveScroll: true });
      },
      onError: (errors) => {
        console.error('Validation errors:', errors);
        alert('Error creating token: ' + Object.values(errors).flat().join(', '));
      },
    });
  };

  const handleUpdateToken = () => {
    if (!selectedToken) return;

    // Prepare the data, converting empty string to null for expires_at
    const requestData = {
      ...formData,
      expires_at: formData.expires_at || null
    };

    router.put(`/api-tokens/${selectedToken.id}`, requestData, {
      onSuccess: () => {
        setShowEditDialog(false);
        setSelectedToken(null);
        setFormData({ name: '', abilities: [], expires_at: '' });
        router.visit(window.location.pathname, { only: ['tokens'], preserveScroll: true });
      },
      onError: (errors) => {
        console.error('Validation errors:', errors);
        alert('Error updating token: ' + Object.values(errors).flat().join(', '));
      },
    });
  };

  const handleDeleteSelected = () => {
    const selectedRows = table.getFilteredSelectedRowModel().rows;
    const selectedIds = selectedRows.map(row => row.original.id);

    if (confirm(`Are you sure you want to delete ${selectedIds.length} API token(s)?`)) {
      router.delete('/api-tokens/delete', {
        data: { ids: selectedIds },
        onSuccess: () => {
          router.visit(window.location.pathname, { only: ['tokens'], preserveScroll: true });
        }
      });
    }
  };

  const handleDeleteToken = () => {
    if (!selectedTokenForDelete) return;
    
    router.delete(`/api-tokens/${selectedTokenForDelete.id}`, {
      onSuccess: () => {
        setIsDeleteDialogOpen(false);
        setSelectedTokenForDelete(null);
        router.visit(window.location.pathname, { only: ['tokens'], preserveScroll: true });
      }
    });
  };

  const copyToClipboard = async (text: string) => {
    try {
      if (navigator.clipboard && window.isSecureContext) {
        // Use the modern clipboard API
        await navigator.clipboard.writeText(text);
      } else {
        // Fallback for older browsers or non-secure contexts
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
          document.execCommand('copy');
        } catch (err) {
          console.error('Failed to copy text: ', err);
        }
        
        document.body.removeChild(textArea);
      }
    } catch (err) {
      console.error('Failed to copy text: ', err);
      // Fallback: show the text in an alert so user can copy manually
      alert(`Please copy this token manually:\n\n${text}`);
    }
  };



  return (
    <AppLayout breadcrumbs={[{ title: 'API Tokens', href: '/api-tokens' }]}>
      <Head title="API Tokens" />
      <div className="w-full p-6">
        {flash?.success && (
          <div className="mb-4 p-4 bg-green-50 border border-green-200 rounded-md">
            <p className="text-green-800">{flash.success}</p>
          </div>
        )}
        {flash?.error && (
          <div className="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
            <p className="text-red-800">{flash.error}</p>
          </div>
        )}
        <div className="flex items-center py-4 gap-4">
          <Input
            placeholder="Filter tokens..."
            value={(table.getColumn("name")?.getFilterValue() as string) ?? ""}
            onChange={(event) =>
              table.getColumn("name")?.setFilterValue(event.target.value)
            }
            className="max-w-sm"
          />
          <Button onClick={() => setShowCreateDialog(true)}>
            <Plus className="mr-2 h-4 w-4" />
            Create Token
          </Button>
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="outline" className="ml-auto">
                Columns <ChevronDown />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" className="max-h-140 overflow-y-auto">
              {table
                .getAllColumns()
                .filter((column) => column.getCanHide())
                .map((column) => {
                  return (
                    <DropdownMenuCheckboxItem
                      key={column.id}
                      className="capitalize"
                      checked={column.getIsVisible()}
                      onCheckedChange={(value) =>
                        column.toggleVisibility(!!value)
                      }
                    >
                      {typeof column.columnDef.header === 'string'
                        ? column.columnDef.header
                        : column.id}
                    </DropdownMenuCheckboxItem>
                  )
                })}
            </DropdownMenuContent>
          </DropdownMenu>
        </div>
        <div className="rounded-md border">
          <Table>
            <TableHeader>
              {table.getHeaderGroups().map((headerGroup) => (
                <TableRow key={headerGroup.id}>
                  {headerGroup.headers.map((header) => {
                    return (
                      <TableHead key={header.id}>
                        {header.isPlaceholder
                          ? null
                          : flexRender(
                              header.column.columnDef.header,
                              header.getContext()
                            )}
                      </TableHead>
                    )
                  })}
                </TableRow>
              ))}
            </TableHeader>
            <TableBody>
              {table.getRowModel().rows?.length ? (
                table.getRowModel().rows.map((row) => (
                  <TableRow
                    key={row.id}
                    data-state={row.getIsSelected() && "selected"}
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
                    No tokens found.
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

      {/* Create Token Dialog */}
      <Dialog open={showCreateDialog} onOpenChange={setShowCreateDialog}>
        <DialogContent className="sm:max-w-[425px]">
          <DialogHeader>
            <DialogTitle>Create API Token</DialogTitle>
          </DialogHeader>
          <div className="grid gap-4 py-4">
            <div className="grid gap-2">
              <Label htmlFor="name">Token Name</Label>
              <Input
                id="name"
                value={formData.name}
                onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                placeholder="Enter token name"
              />
            </div>
            <div className="grid gap-2">
              <Label>Abilities</Label>
              <div className="space-y-2">
                {abilityOptions.map((ability) => (
                  <div key={ability.value} className="flex items-center space-x-2">
                    <Checkbox
                      id={ability.value}
                      checked={formData.abilities.includes(ability.value)}
                      onCheckedChange={(checked) => {
                        if (checked) {
                          setFormData({
                            ...formData,
                            abilities: [...formData.abilities, ability.value]
                          });
                        } else {
                          setFormData({
                            ...formData,
                            abilities: formData.abilities.filter(a => a !== ability.value)
                          });
                        }
                      }}
                    />
                    <Label htmlFor={ability.value} className="text-sm font-normal">
                      {ability.label}
                    </Label>
                  </div>
                ))}
              </div>
            </div>
            <div className="grid gap-2">
              <Label htmlFor="expires_at">Expires At (Optional)</Label>
              <DatePicker
                value={formData.expires_at}
                onChange={(value) => setFormData({ ...formData, expires_at: value })}
                placeholder="Select expiration date and time"
              />
            </div>
          </div>
          <DialogFooter>
            <DialogClose asChild>
              <Button variant="outline">Cancel</Button>
            </DialogClose>
            <Button onClick={handleCreateToken} disabled={!formData.name || formData.abilities.length === 0}>
              Create Token
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* Edit Token Dialog */}
      <Dialog open={showEditDialog} onOpenChange={setShowEditDialog}>
        <DialogContent className="sm:max-w-[425px]">
          <DialogHeader>
            <DialogTitle>Edit API Token</DialogTitle>
          </DialogHeader>
          <div className="grid gap-4 py-4">
            <div className="grid gap-2">
              <Label htmlFor="edit-name">Token Name</Label>
              <Input
                id="edit-name"
                value={formData.name}
                onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                placeholder="Enter token name"
              />
            </div>
            <div className="grid gap-2">
              <Label>Abilities</Label>
              <div className="space-y-2">
                {abilityOptions.map((ability) => (
                  <div key={ability.value} className="flex items-center space-x-2">
                    <Checkbox
                      id={`edit-${ability.value}`}
                      checked={formData.abilities.includes(ability.value)}
                      onCheckedChange={(checked) => {
                        if (checked) {
                          setFormData({
                            ...formData,
                            abilities: [...formData.abilities, ability.value]
                          });
                        } else {
                          setFormData({
                            ...formData,
                            abilities: formData.abilities.filter(a => a !== ability.value)
                          });
                        }
                      }}
                    />
                    <Label htmlFor={`edit-${ability.value}`} className="text-sm font-normal">
                      {ability.label}
                    </Label>
                  </div>
                ))}
              </div>
            </div>
            <div className="grid gap-2">
              <Label htmlFor="edit-expires_at">Expires At (Optional)</Label>
              <DatePicker
                value={formData.expires_at}
                onChange={(value) => setFormData({ ...formData, expires_at: value })}
                placeholder="Select expiration date and time"
              />
            </div>
          </div>
          <DialogFooter>
            <DialogClose asChild>
              <Button variant="outline">Cancel</Button>
            </DialogClose>
            <Button onClick={handleUpdateToken} disabled={!formData.name || formData.abilities.length === 0}>
              Update Token
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* New Token Display Dialog */}
      <Dialog open={showNewTokenDialog} onOpenChange={setShowNewTokenDialog}>
        <DialogContent className="sm:max-w-[500px]">
          <DialogHeader>
            <DialogTitle>API Token Created</DialogTitle>
          </DialogHeader>
          <div className="space-y-4">
            <div className="rounded-md bg-muted p-4">
              <p className="text-sm text-muted-foreground mb-2">
                Make sure to copy your API token now. You won't be able to see it again!
              </p>
              <div className="flex items-center gap-2">
                <textarea
                  value={currentNewToken?.plain_text_token || ''}
                  readOnly
                  className="flex min-h-[60px] w-full rounded-md border bg-transparent px-3 py-2 text-sm font-mono resize-none"
                  rows={3}
                />
                <Button
                  variant="outline"
                  size="icon"
                  onClick={() => copyToClipboard(currentNewToken?.plain_text_token || '')}
                >
                  <Copy className="h-4 w-4" />
                </Button>
              </div>
            </div>
            <div className="text-sm text-muted-foreground">
              <p>Use this token in your API requests with the Authorization header:</p>
              <code className="block mt-2 p-2 bg-muted rounded font-mono text-xs">
                Authorization: Bearer {currentNewToken?.plain_text_token}
              </code>
            </div>
          </div>
          <DialogFooter>
            <DialogClose asChild>
              <Button>I've copied the token</Button>
            </DialogClose>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* Delete Token Dialog */}
      <Dialog open={isDeleteDialogOpen} onOpenChange={setIsDeleteDialogOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Delete API Token</DialogTitle>
          </DialogHeader>
          <p>Are you sure you want to delete the API token "{selectedTokenForDelete?.name}"? This action cannot be undone.</p>
          <DialogFooter>
            <DialogClose asChild>
              <Button variant="outline">Cancel</Button>
            </DialogClose>
            <Button variant="destructive" onClick={handleDeleteToken}>Delete Token</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

    </AppLayout>
  );
} 