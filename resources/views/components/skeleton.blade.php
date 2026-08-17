<div class="animate-pulse flex flex-col space-y-4 p-4">
    @for($i = 0; $i < ($count ?? 3); $i++)
    <div class="flex items-center justify-between py-3 border-b border-border last:border-0">
        <div class="flex items-center space-x-4">
            <div class="h-10 w-10 bg-gray-200 dark:bg-gray-700 rounded-xl"></div>
            <div class="space-y-2">
                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-32"></div>
                <div class="h-3 bg-gray-100 dark:bg-gray-800 rounded w-24"></div>
            </div>
        </div>
        <div class="space-y-2 text-right">
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-16 ml-auto"></div>
            <div class="h-3 bg-gray-100 dark:bg-gray-800 rounded w-20 ml-auto"></div>
        </div>
    </div>
    @endfor
</div>
