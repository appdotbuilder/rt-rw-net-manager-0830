import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

export default function Welcome() {
    const { auth } = usePage<SharedData>().props;

    return (
        <>
            <Head title="RT/RW-Net Management System">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
            </Head>
            <div className="flex min-h-screen flex-col items-center bg-gradient-to-br from-blue-50 to-indigo-100 p-6 text-gray-800 lg:justify-center lg:p-8 dark:from-gray-900 dark:to-gray-800 dark:text-gray-100">
                <header className="mb-8 w-full max-w-6xl">
                    <nav className="flex items-center justify-end gap-4">
                        {auth.user ? (
                            <Link
                                href={route('dashboard')}
                                className="inline-flex items-center rounded-lg bg-blue-600 px-6 py-3 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                            >
                                🏠 Dashboard
                            </Link>
                        ) : (
                            <>
                                <Link
                                    href={route('login')}
                                    className="inline-flex items-center rounded-lg border border-gray-300 px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                                >
                                    🔑 Log in
                                </Link>
                                <Link
                                    href={route('register')}
                                    className="inline-flex items-center rounded-lg bg-blue-600 px-6 py-3 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                                >
                                    📝 Register
                                </Link>
                            </>
                        )}
                    </nav>
                </header>

                <div className="w-full max-w-6xl">
                    <main className="text-center">
                        {/* Hero Section */}
                        <div className="mb-16">
                            <div className="mb-6 text-6xl">🌐</div>
                            <h1 className="mb-4 text-4xl font-bold text-gray-900 dark:text-white lg:text-6xl">
                                RT/RW-Net Management
                            </h1>
                            <p className="mb-8 text-xl text-gray-600 dark:text-gray-300 lg:text-2xl">
                                Sistem manajemen lengkap untuk provider internet RT/RW dengan integrasi Mikrotik
                            </p>
                            
                            {!auth.user && (
                                <div className="flex flex-col gap-4 sm:flex-row sm:justify-center">
                                    <Link
                                        href={route('login')}
                                        className="inline-flex items-center justify-center rounded-lg bg-blue-600 px-8 py-4 text-lg font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                                    >
                                        🚀 Mulai Sekarang
                                    </Link>
                                    <Link
                                        href={route('register')}
                                        className="inline-flex items-center justify-center rounded-lg border-2 border-blue-600 px-8 py-4 text-lg font-semibold text-blue-600 hover:bg-blue-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors dark:border-blue-400 dark:text-blue-400"
                                    >
                                        👨‍💼 Daftar Admin
                                    </Link>
                                </div>
                            )}
                        </div>

                        {/* Features Grid */}
                        <div className="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                            {/* Customer Management */}
                            <div className="rounded-xl bg-white p-8 shadow-lg dark:bg-gray-800">
                                <div className="mb-4 text-4xl">👥</div>
                                <h3 className="mb-3 text-xl font-semibold text-gray-900 dark:text-white">
                                    Manajemen Pelanggan
                                </h3>
                                <ul className="space-y-2 text-left text-gray-600 dark:text-gray-300">
                                    <li>• CRUD data pelanggan lengkap</li>
                                    <li>• Auto-generate username & password PPPoE</li>
                                    <li>• Upload foto KTP pelanggan</li>
                                    <li>• Status aktif/nonaktif/suspended</li>
                                </ul>
                            </div>

                            {/* Mikrotik Integration */}
                            <div className="rounded-xl bg-white p-8 shadow-lg dark:bg-gray-800">
                                <div className="mb-4 text-4xl">🔗</div>
                                <h3 className="mb-3 text-xl font-semibold text-gray-900 dark:text-white">
                                    Integrasi Mikrotik
                                </h3>
                                <ul className="space-y-2 text-left text-gray-600 dark:text-gray-300">
                                    <li>• Koneksi via RouterOS API</li>
                                    <li>• Sinkronisasi PPP Secret otomatis</li>
                                    <li>• Enable/disable akun real-time</li>
                                    <li>• Monitor status Online/Offline</li>
                                </ul>
                            </div>

                            {/* Billing System */}
                            <div className="rounded-xl bg-white p-8 shadow-lg dark:bg-gray-800">
                                <div className="mb-4 text-4xl">💰</div>
                                <h3 className="mb-3 text-xl font-semibold text-gray-900 dark:text-white">
                                    Sistem Billing
                                </h3>
                                <ul className="space-y-2 text-left text-gray-600 dark:text-gray-300">
                                    <li>• Generate tagihan bulanan otomatis</li>
                                    <li>• Input pembayaran & upload bukti</li>
                                    <li>• Tracking tagihan tertunggak</li>
                                    <li>• Laporan pendapatan per bulan</li>
                                </ul>
                            </div>

                            {/* Package Management */}
                            <div className="rounded-xl bg-white p-8 shadow-lg dark:bg-gray-800">
                                <div className="mb-4 text-4xl">📦</div>
                                <h3 className="mb-3 text-xl font-semibold text-gray-900 dark:text-white">
                                    Manajemen Paket
                                </h3>
                                <ul className="space-y-2 text-left text-gray-600 dark:text-gray-300">
                                    <li>• Kelola paket internet</li>
                                    <li>• Set harga & bandwidth</li>
                                    <li>• Deskripsi paket lengkap</li>
                                    <li>• Status aktif/nonaktif paket</li>
                                </ul>
                            </div>

                            {/* Reports & Analytics */}
                            <div className="rounded-xl bg-white p-8 shadow-lg dark:bg-gray-800">
                                <div className="mb-4 text-4xl">📊</div>
                                <h3 className="mb-3 text-xl font-semibold text-gray-900 dark:text-white">
                                    Laporan & Analitik
                                </h3>
                                <ul className="space-y-2 text-left text-gray-600 dark:text-gray-300">
                                    <li>• Dashboard statistik lengkap</li>
                                    <li>• Laporan pendapatan bulanan</li>
                                    <li>• Export ke PDF/Excel</li>
                                    <li>• Monitor pelanggan aktif</li>
                                </ul>
                            </div>

                            {/* Notifications */}
                            <div className="rounded-xl bg-white p-8 shadow-lg dark:bg-gray-800">
                                <div className="mb-4 text-4xl">🔔</div>
                                <h3 className="mb-3 text-xl font-semibold text-gray-900 dark:text-white">
                                    Notifikasi & Reminder
                                </h3>
                                <ul className="space-y-2 text-left text-gray-600 dark:text-gray-300">
                                    <li>• Reminder jatuh tempo via email</li>
                                    <li>• Auto suspend pelanggan telat</li>
                                    <li>• Jadwal pengecekan harian</li>
                                    <li>• Placeholder WhatsApp gateway</li>
                                </ul>
                            </div>
                        </div>

                        {/* Tech Stack */}
                        <div className="mt-16 rounded-xl bg-white p-8 shadow-lg dark:bg-gray-800">
                            <h3 className="mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
                                ⚡ Tech Stack Modern
                            </h3>
                            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                <div className="flex items-center justify-center rounded-lg bg-red-50 p-4 dark:bg-red-900/20">
                                    <span className="text-red-600 dark:text-red-400">🚀 Laravel 11</span>
                                </div>
                                <div className="flex items-center justify-center rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                                    <span className="text-blue-600 dark:text-blue-400">⚛️ React + Inertia</span>
                                </div>
                                <div className="flex items-center justify-center rounded-lg bg-cyan-50 p-4 dark:bg-cyan-900/20">
                                    <span className="text-cyan-600 dark:text-cyan-400">🎨 Tailwind CSS</span>
                                </div>
                                <div className="flex items-center justify-center rounded-lg bg-green-50 p-4 dark:bg-green-900/20">
                                    <span className="text-green-600 dark:text-green-400">🔐 Laravel Breeze</span>
                                </div>
                            </div>
                        </div>

                        {/* Footer */}
                        <footer className="mt-16 text-sm text-gray-500 dark:text-gray-400">
                            <p>
                                🏗️ Dibangun dengan teknologi terdepan untuk efisiensi maksimal RT/RW-Net Anda
                            </p>
                            <p className="mt-2">
                                Dibuat dengan ❤️ menggunakan{" "}
                                <a 
                                    href="https://laravel.com" 
                                    target="_blank" 
                                    rel="noopener noreferrer"
                                    className="font-medium text-blue-600 hover:underline dark:text-blue-400"
                                >
                                    Laravel
                                </a>
                                {" & "}
                                <a 
                                    href="https://reactjs.org" 
                                    target="_blank" 
                                    rel="noopener noreferrer"
                                    className="font-medium text-blue-600 hover:underline dark:text-blue-400"
                                >
                                    React
                                </a>
                            </p>
                        </footer>
                    </main>
                </div>
            </div>
        </>
    );
}