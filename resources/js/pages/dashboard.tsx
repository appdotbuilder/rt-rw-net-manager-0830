import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { 
    Users, 
    Wifi, 
    CreditCard, 
    TrendingUp, 
    AlertCircle,
    DollarSign,
    Activity,
    Package
} from 'lucide-react';

interface DashboardProps {
    stats: {
        totalCustomers: number;
        activeCustomers: number;
        totalPackages: number;
        unpaidBills: number;
        overdueBills: number;
        monthlyRevenue: number;
    };
    recentCustomers: Array<{
        id: number;
        nama: string;
        paket: {
            nama_paket: string;
        };
        created_at: string;
    }>;
    recentPayments: Array<{
        id: number;
        jumlah: number;
        tanggal_bayar: string;
        tagihan: {
            customer: {
                nama: string;
            };
        };
    }>;
    [key: string]: unknown;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

export default function Dashboard({ stats, recentCustomers, recentPayments }: DashboardProps) {
    const formatCurrency = (amount: number) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(amount);
    };

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard RT/RW-Net" />
            
            <div className="space-y-6 p-6">
                {/* Header */}
                <div>
                    <h1 className="text-3xl font-bold text-gray-900 dark:text-white">
                        🌐 Dashboard RT/RW-Net
                    </h1>
                    <p className="text-gray-600 dark:text-gray-300 mt-2">
                        Ringkasan sistem manajemen jaringan RT/RW
                    </p>
                </div>

                {/* Stats Grid */}
                <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                    {/* Total Customers */}
                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div className="flex items-center">
                            <div className="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                                <Users className="h-6 w-6 text-blue-600 dark:text-blue-300" />
                            </div>
                            <div className="ml-4">
                                <p className="text-sm font-medium text-gray-600 dark:text-gray-300">
                                    Total Pelanggan
                                </p>
                                <p className="text-2xl font-semibold text-gray-900 dark:text-white">
                                    {stats.totalCustomers}
                                </p>
                            </div>
                        </div>
                        <div className="mt-4">
                            <span className="text-sm text-green-600 dark:text-green-400">
                                {stats.activeCustomers} aktif
                            </span>
                        </div>
                    </div>

                    {/* Monthly Revenue */}
                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div className="flex items-center">
                            <div className="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                                <DollarSign className="h-6 w-6 text-green-600 dark:text-green-300" />
                            </div>
                            <div className="ml-4">
                                <p className="text-sm font-medium text-gray-600 dark:text-gray-300">
                                    Pendapatan Bulan Ini
                                </p>
                                <p className="text-2xl font-semibold text-gray-900 dark:text-white">
                                    {formatCurrency(stats.monthlyRevenue)}
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* Unpaid Bills */}
                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div className="flex items-center">
                            <div className="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                                <CreditCard className="h-6 w-6 text-yellow-600 dark:text-yellow-300" />
                            </div>
                            <div className="ml-4">
                                <p className="text-sm font-medium text-gray-600 dark:text-gray-300">
                                    Tagihan Belum Lunas
                                </p>
                                <p className="text-2xl font-semibold text-gray-900 dark:text-white">
                                    {stats.unpaidBills}
                                </p>
                            </div>
                        </div>
                        <div className="mt-4">
                            <span className="text-sm text-red-600 dark:text-red-400">
                                {stats.overdueBills} terlambat
                            </span>
                        </div>
                    </div>

                    {/* Total Packages */}
                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div className="flex items-center">
                            <div className="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                                <Package className="h-6 w-6 text-purple-600 dark:text-purple-300" />
                            </div>
                            <div className="ml-4">
                                <p className="text-sm font-medium text-gray-600 dark:text-gray-300">
                                    Paket Internet
                                </p>
                                <p className="text-2xl font-semibold text-gray-900 dark:text-white">
                                    {stats.totalPackages}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Quick Actions */}
                <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        🚀 Aksi Cepat
                    </h3>
                    <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                        <Link
                            href="/customers/create"
                            className="flex items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors"
                        >
                            <Users className="h-6 w-6 text-blue-600 dark:text-blue-300 mr-3" />
                            <span className="font-medium text-blue-700 dark:text-blue-300">
                                Tambah Pelanggan
                            </span>
                        </Link>

                        <Link
                            href="/paket/create"
                            className="flex items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors"
                        >
                            <Wifi className="h-6 w-6 text-green-600 dark:text-green-300 mr-3" />
                            <span className="font-medium text-green-700 dark:text-green-300">
                                Tambah Paket
                            </span>
                        </Link>

                        <Link
                            href="/pembayaran/create"
                            className="flex items-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-colors"
                        >
                            <CreditCard className="h-6 w-6 text-yellow-600 dark:text-yellow-300 mr-3" />
                            <span className="font-medium text-yellow-700 dark:text-yellow-300">
                                Input Pembayaran
                            </span>
                        </Link>

                        <Link
                            href="/reports"
                            className="flex items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors"
                        >
                            <TrendingUp className="h-6 w-6 text-purple-600 dark:text-purple-300 mr-3" />
                            <span className="font-medium text-purple-700 dark:text-purple-300">
                                Lihat Laporan
                            </span>
                        </Link>
                    </div>
                </div>

                <div className="grid gap-6 lg:grid-cols-2">
                    {/* Recent Customers */}
                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div className="flex items-center justify-between mb-4">
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                                👥 Pelanggan Terbaru
                            </h3>
                            <Link
                                href="/customers"
                                className="text-sm text-blue-600 dark:text-blue-400 hover:underline"
                            >
                                Lihat Semua
                            </Link>
                        </div>
                        <div className="space-y-3">
                            {recentCustomers.map((customer) => (
                                <div key={customer.id} className="flex items-center justify-between">
                                    <div>
                                        <p className="font-medium text-gray-900 dark:text-white">
                                            {customer.nama}
                                        </p>
                                        <p className="text-sm text-gray-600 dark:text-gray-300">
                                            {customer.paket.nama_paket}
                                        </p>
                                    </div>
                                    <span className="text-sm text-gray-500 dark:text-gray-400">
                                        {formatDate(customer.created_at)}
                                    </span>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Recent Payments */}
                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div className="flex items-center justify-between mb-4">
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                                💰 Pembayaran Terbaru
                            </h3>
                            <Link
                                href="/pembayaran"
                                className="text-sm text-blue-600 dark:text-blue-400 hover:underline"
                            >
                                Lihat Semua
                            </Link>
                        </div>
                        <div className="space-y-3">
                            {recentPayments.map((payment) => (
                                <div key={payment.id} className="flex items-center justify-between">
                                    <div>
                                        <p className="font-medium text-gray-900 dark:text-white">
                                            {payment.tagihan.customer.nama}
                                        </p>
                                        <p className="text-sm text-green-600 dark:text-green-400">
                                            {formatCurrency(payment.jumlah)}
                                        </p>
                                    </div>
                                    <span className="text-sm text-gray-500 dark:text-gray-400">
                                        {formatDate(payment.tanggal_bayar)}
                                    </span>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>

                {/* System Status */}
                <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        🔌 Status Sistem
                    </h3>
                    <div className="grid gap-4 md:grid-cols-3">
                        <div className="flex items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <Activity className="h-6 w-6 text-green-600 dark:text-green-300 mr-3" />
                            <div>
                                <p className="font-medium text-green-700 dark:text-green-300">
                                    Sistem Online
                                </p>
                                <p className="text-sm text-green-600 dark:text-green-400">
                                    Berjalan normal
                                </p>
                            </div>
                        </div>

                        <Link
                            href="/mikrotik/1"
                            className="flex items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors"
                        >
                            <Wifi className="h-6 w-6 text-blue-600 dark:text-blue-300 mr-3" />
                            <div>
                                <p className="font-medium text-blue-700 dark:text-blue-300">
                                    Status Mikrotik
                                </p>
                                <p className="text-sm text-blue-600 dark:text-blue-400">
                                    Cek koneksi online
                                </p>
                            </div>
                        </Link>

                        <Link
                            href="/mikrotik"
                            className="flex items-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-colors"
                        >
                            <AlertCircle className="h-6 w-6 text-yellow-600 dark:text-yellow-300 mr-3" />
                            <div>
                                <p className="font-medium text-yellow-700 dark:text-yellow-300">
                                    Konfigurasi
                                </p>
                                <p className="text-sm text-yellow-600 dark:text-yellow-400">
                                    Setup router
                                </p>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}