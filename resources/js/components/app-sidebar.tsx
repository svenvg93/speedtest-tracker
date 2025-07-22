import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { Button } from '@/components/ui/button';
import SpeedtestDialog from '@/components/SpeedtestDialog';
import { type NavItem, type NavGroup } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { BookOpen, ChartSpline, Table, GitCompare, Users, Play, Key } from 'lucide-react';
import * as React from "react";
import AppLogo from './app-logo';

const getMainNavItems = (user: any): NavItem[] => {
    const items: NavItem[] = [
        {
            title: 'Dashboard',
            url: '/dashboard',
            icon:  ChartSpline,
        },
        {
            title: 'Results',
            url: '/results',
            icon:  Table,
        },
    ];

    // Only show API Tokens for admin users
    if (user?.is_admin) {
        items.push({
            title: 'API Tokens',
            url: '/api-tokens',
            icon:  Key,
        });
    }

    return items;
};

const getApplicationNavItems = (user: any): NavItem[] => {
    const items: NavItem[] = [];
    
    if (user?.is_admin) {
        items.push({
            title: 'Users',
            url: '/users',
            icon:  Users,
        });
    }
    
    return items;
};

const footerNavItems: NavItem[] = [
    {
        title: 'Github',
        url: 'https://github.com/alexjustesen/speedtest-tracker',
        icon: GitCompare ,
    },
    {
        title: 'Documentation',
        url: 'https://docs.speedtest-tracker.dev',
        icon: BookOpen,
    },
];

export function AppSidebar() {
    const user = (usePage().props as any)?.auth?.user;
    const [speedtestDialogOpen, setSpeedtestDialogOpen] = React.useState(false);

    return (
        <>
            <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/dashboard" prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                    <SidebarMenuItem>
                        <SidebarMenuButton
                            tooltip="Run Speedtest"
                            onClick={() => setSpeedtestDialogOpen(true)}
                            className="bg-primary text-primary-foreground hover:bg-primary/90 hover:text-primary-foreground active:bg-primary/90 active:text-primary-foreground min-w-8 duration-200 ease-linear"
                        >
                            <Play className="h-5 w-5" />
                            <span>Run Speedtest</span>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={getMainNavItems(user)} groupLabel="Home" />
                {(() => {
                    const appItems = getApplicationNavItems(user);
                    return appItems.length > 0 ? (
                        <NavMain items={appItems} groupLabel="Application" />
                    ) : null;
                })()}
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser user={user} />
            </SidebarFooter>
        </Sidebar>
        
        <SpeedtestDialog 
            open={speedtestDialogOpen} 
            onOpenChange={setSpeedtestDialogOpen} 
        />
    </>
    );
}
