import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { 
    BookOpen, 
    Folder, 
    LayoutGrid, 
    Users, 
    Wifi, 
    CreditCard, 
    Receipt, 
    TrendingUp, 
    Settings,
    Activity
} from 'lucide-react';
import AppLogo from './app-logo';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Pelanggan',
        href: '/customers',
        icon: Users,
    },
    {
        title: 'Paket Internet',
        href: '/paket',
        icon: Wifi,
    },
    {
        title: 'Tagihan',
        href: '/tagihan',
        icon: Receipt,
    },
    {
        title: 'Pembayaran',
        href: '/pembayaran',
        icon: CreditCard,
    },
    {
        title: 'Laporan',
        href: '/reports',
        icon: TrendingUp,
    },
    {
        title: 'Status Mikrotik',
        href: '/mikrotik/1',
        icon: Activity,
    },
    {
        title: 'Konfigurasi',
        href: '/mikrotik',
        icon: Settings,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'RT/RW Net Docs',
        href: 'https://laravel.com/docs',
        icon: BookOpen,
    },
    {
        title: 'Mikrotik Wiki',
        href: 'https://wiki.mikrotik.com/',
        icon: Folder,
    },
];

export function AppSidebar() {
    return (
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
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
