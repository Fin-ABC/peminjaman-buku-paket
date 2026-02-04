<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Perpustakaan SMKN 1 Sumedang' }}</title>
    @vite('resources/css/app.css', 'resources/js/app.js')

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-50 flex">
    {{-- Sidebar Start --}}
    <div class="flex h-screen w-64 flex-col justify-between border-e bg-white dark:bg-gray-900 dark:border-gray-800">
        <div class="px-4 py-6">
            <span class="ml-4 flex items-center gap-1 text-gray-700 dark:text-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-books">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M5 5a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1l0 -14" />
                    <path d="M9 5a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1l0 -14" />
                    <path d="M5 8h4" />
                    <path d="M9 16h4" />
                    <path
                        d="M13.803 4.56l2.184 -.53c.562 -.135 1.133 .19 1.282 .732l3.695 13.418a1.02 1.02 0 0 1 -.634 1.219l-.133 .041l-2.184 .53c-.562 .135 -1.133 -.19 -1.282 -.732l-3.695 -13.418a1.02 1.02 0 0 1 .634 -1.219l.133 -.041" />
                    <path d="M14 9l4 -1" />
                    <path d="M16 16l3.923 -.98" />
                </svg>
                <span class="text-xl font-medium tracking-widest uppercase">bashlib</span>
            </span>

            <ul class="mt-6 space-y-1">
                <li>
                    <a href="#"
                        class="block rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-200">
                        General
                    </a>
                </li>

                <li>
                    <details class="group [&_summary::-webkit-details-marker]:hidden">
                        <summary
                            class="flex cursor-pointer items-center justify-between rounded-lg px-4 py-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200">
                            <span class="text-sm font-medium"> Teams </span>

                            <span class="shrink-0 transition duration-300 group-open:-rotate-180">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </summary>

                        <ul class="mt-2 space-y-1 px-4">
                            <li>
                                <a href="#"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200">
                                    Banned Users
                                </a>
                            </li>

                            <li>
                                <a href="#"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200">
                                    Calendar
                                </a>
                            </li>
                        </ul>
                    </details>
                </li>

                <li>
                    <a href="#"
                        class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200">
                        Billing
                    </a>
                </li>

                <li>
                    <a href="#"
                        class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200">
                        Invoices
                    </a>
                </li>

                <li>
                    <details class="group [&_summary::-webkit-details-marker]:hidden">
                        <summary
                            class="flex cursor-pointer items-center justify-between rounded-lg px-4 py-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200">
                            <span class="text-sm font-medium"> Account </span>

                            <span class="shrink-0 transition duration-300 group-open:-rotate-180">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </summary>

                        <ul class="mt-2 space-y-1 px-4">
                            <li>
                                <a href="#"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200">
                                    Details
                                </a>
                            </li>

                            <li>
                                <a href="#"
                                    class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200">
                                    Security
                                </a>
                            </li>

                            <li>
                                <form action="#">
                                    <button type="submit"
                                        class="w-full rounded-lg px-4 py-2 text-sm font-medium text-gray-500 [text-align:inherit] hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200">
                                        Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </details>
                </li>
            </ul>
        </div>

        <div class="sticky inset-x-0 bottom-0 border-t border-gray-100 dark:border-gray-800">
            <a href="#"
                class="flex items-center gap-2 bg-white p-4 hover:bg-gray-50 dark:bg-gray-900 dark:hover:bg-gray-800">
                <img alt=""
                    src="https://images.unsplash.com/photo-1600486913747-55e5470d6f40?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1770&q=80"
                    class="size-10 rounded-full object-cover" />

                <div>
                    <p class="text-xs">
                        <strong class="block font-medium text-gray-900 dark:text-white"> Eric Frusciante </strong>

                        <span> eric@frusciante.com </span>
                    </p>
                </div>
            </a>
        </div>
    </div>
    {{-- Sidebar End --}}

    <div>
        {{ $slot }}
    </div>
</body>

</html>
