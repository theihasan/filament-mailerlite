<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        @php
            $stats = $this->getStats();
        @endphp
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 rounded-md bg-blue-500 bg-opacity-10">
                    <x-heroicon-o-users class="w-6 h-6 text-blue-500" />
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $stats['subscribers'] }}</h3>
                    <p class="text-sm text-gray-500">Total Subscribers</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 rounded-md bg-green-500 bg-opacity-10">
                    <x-heroicon-o-megaphone class="w-6 h-6 text-green-500" />
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $stats['campaigns'] }}</h3>
                    <p class="text-sm text-gray-500">Total Campaigns</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 rounded-md bg-purple-500 bg-opacity-10">
                    <x-heroicon-o-user-group class="w-6 h-6 text-purple-500" />
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $stats['groups'] }}</h3>
                    <p class="text-sm text-gray-500">Total Groups</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
            <p class="text-sm text-gray-500 mt-1">Manage your MailerLite account with these quick actions</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <button class="group flex flex-col items-center p-6 rounded-lg border-2 border-dashed border-gray-300 hover:border-blue-500 hover:bg-blue-50 transition-all duration-200">
                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 group-hover:bg-blue-200 mb-3">
                        <x-heroicon-o-user-plus class="w-6 h-6 text-blue-600" />
                    </div>
                    <span class="text-sm font-medium text-gray-900 group-hover:text-blue-900">Add Subscriber</span>
                    <span class="text-xs text-gray-500 mt-1 text-center">Add new subscribers to your lists</span>
                </button>

                <a href="#" class="group flex flex-col items-center p-6 rounded-lg border-2 border-dashed border-gray-300 hover:border-green-500 hover:bg-green-50 transition-all duration-200">
                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-green-100 group-hover:bg-green-200 mb-3">
                        <x-heroicon-o-megaphone class="w-6 h-6 text-green-600" />
                    </div>
                    <span class="text-sm font-medium text-gray-900 group-hover:text-green-900">Create Campaign</span>
                    <span class="text-xs text-gray-500 mt-1 text-center">Design and send email campaigns</span>
                </a>

                <a href="#" class="group flex flex-col items-center p-6 rounded-lg border-2 border-dashed border-gray-300 hover:border-purple-500 hover:bg-purple-50 transition-all duration-200">
                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-purple-100 group-hover:bg-purple-200 mb-3">
                        <x-heroicon-o-user-group class="w-6 h-6 text-purple-600" />
                    </div>
                    <span class="text-sm font-medium text-gray-900 group-hover:text-purple-900">Manage Groups</span>
                    <span class="text-xs text-gray-500 mt-1 text-center">Organize subscribers into groups</span>
                </a>

                <a href="#" class="group flex flex-col items-center p-6 rounded-lg border-2 border-dashed border-gray-300 hover:border-gray-500 hover:bg-gray-50 transition-all duration-200">
                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 group-hover:bg-gray-200 mb-3">
                        <x-heroicon-o-cog-6-tooth class="w-6 h-6 text-gray-600" />
                    </div>
                    <span class="text-sm font-medium text-gray-900">Settings</span>
                    <span class="text-xs text-gray-500 mt-1 text-center">Configure your account settings</span>
                </a>
            </div>
        </div>
    </div>
</x-filament-panels::page>
