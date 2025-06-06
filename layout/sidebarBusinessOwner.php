<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            overflow: auto !important;
            padding-right: 0 !important;
        }

        .rotate-180 {
            transform: rotate(180deg);
            transition: transform 0.2s ease-in-out;
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <!-- drawer component -->
    <div id="drawer-navigation" class="fixed top-0 left-0 z-40 w-64 h-screen p-4 overflow-y-auto bg-[#E1CDB1]" tabindex="-1" aria-labelledby="drawer-navigation-label">
        <img src="../../assets/gambar/logo.png" alt="logoSimaBer" class="mix-blend-multiply mx-0 w-24 h-24">
        <div class="py-4 overflow-y-auto">
            <ul class="space-y-2 font-sm">
                <li>
                    <a href="dashboardBusinessOwner.php" class="flex items-center p-2 rounded-lg group <?php echo $currentPage === 'dashboardBusinessOwner.php' ? 'bg-[#FFEEDB] font-semibold text-black border-l-4 border-black rounded-l-none' : 'text-gray-900 dark:hover:bg-[#FFEEDB]'; ?>">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 12C6.20914 12 8 13.7909 8 16C8 18.2091 6.20914 20 4 20C1.79086 20 0 18.2091 0 16C0 13.7909 1.79086 12 4 12ZM18 12C19.1046 12 20 12.8954 20 14V18C20 19.1046 19.1046 20 18 20H14C12.8954 20 12 19.1046 12 18V14C12 12.8954 12.8954 12 14 12H18ZM6 0C7.10457 0 8 0.895431 8 2V6C8 7.10457 7.10457 8 6 8H2C0.895431 8 0 7.10457 0 6V2C0 0.895431 0.895431 0 2 0H6ZM16 0C18.2091 0 20 1.79086 20 4C20 6.20914 18.2091 8 16 8C13.7909 8 12 6.20914 12 4C12 1.79086 13.7909 0 16 0Z" fill="black" />
                        </svg>
                        <span class="ms-3">Dashboard</span>
                    </a>
                </li>

                <li>
                    <button data-dropdown-target="stock-dropdown" class="flex items-center justify-between w-full p-2 text-gray-900 rounded-lg dark:hover:bg-[#FFEEDB] group <?php echo $currentPage === 'riceStock.php' || $currentPage === 'orderSupplier.php' || $currentPage === 'orderStatusSupplier.php' || $currentPage === 'historySupplier.php' ? 'bg-[#FFEEDB] font-semibold text-black border-l-4 border-black rounded-l-none' : 'text-gray-900 dark:hover:bg-[#FFEEDB]'; ?>">
                        <div class="flex items-center ">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 6L9 7C9 8.65685 10.3431 10 12 10C13.6569 10 15 8.65685 15 7V6" stroke="#28303F" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M15.6116 3H8.3886C6.43325 3 4.76449 4.41365 4.44303 6.3424L2.77636 16.3424C2.37001 18.7805 4.25018 21 6.72194 21H17.2783C19.75 21 21.6302 18.7805 21.2238 16.3424L19.5572 6.3424C19.2357 4.41365 17.5669 3 15.6116 3Z" stroke="#28303F" stroke-width="1.3" stroke-linejoin="round" />
                            </svg>
                            <span class="ms-3">Stock Management</span>
                        </div>
                        <svg class="dropdown-icon transition-transform" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 10L12 14L17 10" stroke="#28303F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <ul id="stock-dropdown" class="dropdown-list hidden py-2 space-y-2">
                        <li>
                            <a href="riceStock.php" class="flex items-center p-2 pl-11 rounded-lg text-gray-900 dark:hover:bg-[#FFEEDB] <?php echo $currentPage === 'riceStock.php' ? 'bg-[#FFEEDB] font-semibold' : ''; ?>">
                                Rice Stock
                            </a>
                        </li>
                        <li>
                            <a href="orderSupplier.php" class="flex items-center p-2 pl-11 rounded-lg text-gray-900 dark:hover:bg-[#FFEEDB] <?php echo $currentPage === 'orderSupplier.php' ? 'bg-[#FFEEDB] font-semibold' : ''; ?>">
                                Order
                            </a>
                        </li>
                        <li>
                            <a href="orderStatusSupplier.php" class="flex items-center p-2 pl-11 rounded-lg text-gray-900 dark:hover:bg-[#FFEEDB] <?php echo $currentPage === 'orderStatusSupplier.php' ? 'bg-[#FFEEDB] font-semibold' : ''; ?>">
                                Order Status
                            </a>
                        </li>
                        <li>
                            <a href="historySupplier.php" class="flex items-center p-2 pl-11 rounded-lg text-gray-900 dark:hover:bg-[#FFEEDB] <?php echo $currentPage === 'historySupplier.php' ? 'bg-[#FFEEDB] font-semibold' : ''; ?>">
                                History Supplier
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <button data-dropdown-target="relationship-dropdown" class="flex items-center justify-between w-full p-2 text-gray-900 rounded-lg dark:hover:bg-[#FFEEDB] group <?php echo $currentPage === 'supplier.php' || $currentPage === 'customer.php' || $currentPage === 'orderSupplier.php' ? 'bg-[#FFEEDB] font-semibold text-black border-l-4 border-black rounded-l-none' : 'text-gray-900 dark:hover:bg-[#FFEEDB]'; ?>">
                        <div class="flex items-center">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10.8333 12.0832C13.2266 12.0832 15.1667 10.1431 15.1667 7.74984C15.1667 5.3566 13.2266 3.4165 10.8333 3.4165C8.4401 3.4165 6.5 5.3566 6.5 7.74984C6.5 10.1431 8.4401 12.0832 10.8333 12.0832ZM10.8333 22.9165C15.0215 22.9165 18.4167 20.9764 18.4167 18.5832C18.4167 16.1899 15.0215 14.2498 10.8333 14.2498C6.64517 14.2498 3.25 16.1899 3.25 18.5832C3.25 20.9764 6.64517 22.9165 10.8333 22.9165Z" fill="#28303F" />
                            </svg>
                            <span class="ms-3">Relationship <br> Management</span>
                        </div>
                        <svg class="dropdown-icon transition-transform" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 10L12 14L17 10" stroke="#28303F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <ul id="relationship-dropdown" class="dropdown-list hidden py-2 space-y-2">
                        <li>
                            <a href="supplier.php" class="flex items-center p-2 pl-11 rounded-lg text-gray-900 dark:hover:bg-[#FFEEDB] <?php echo $currentPage === 'supplier.php' ? 'bg-[#FFEEDB] font-semibold text-black border-l-4 border-black rounded-l-none' : 'text-gray-900 dark:hover:bg-[#FFEEDB]'; ?>">
                                Supplier
                            </a>
                        </li>
                        <li>
                            <a href="customer.php" class="flex items-center p-2 pl-11 rounded-lg text-gray-900 dark:hover:bg-[#FFEEDB] <?php echo $currentPage === 'customer.php' ? 'bg-[#FFEEDB] font-semibold text-black border-l-4 border-black rounded-l-none' : 'text-gray-900 dark:hover:bg-[#FFEEDB]'; ?>">
                                Customer
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <button data-dropdown-target="transaction-dropdown" class="flex items-center justify-between w-full p-2 text-gray-900 rounded-lg dark:hover:bg-[#FFEEDB] group">
                        <div class="flex items-center">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_2668_2805)">
                                    <path d="M22 6H6C3.79086 6 2 7.79086 2 10V18C2 20.2091 3.79086 22 6 22H18C20.2091 22 22 20.2091 22 18V6Z" stroke="#28303F" stroke-width="1.5" stroke-linejoin="round" />
                                    <path d="M22 6C22 3.79086 20.2091 2 18 2H12C9.79086 2 8 3.79086 8 6V6H22V6Z" stroke="#28303F" stroke-width="1.5" stroke-linejoin="round" />
                                    <path d="M2 12L2 16L6 16C7.10457 16 8 15.1046 8 14V14C8 12.8954 7.10457 12 6 12L2 12Z" stroke="#28303F" stroke-width="1.5" stroke-linejoin="round" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_2668_2805"><rect width="24" height="24" fill="white"/></clipPath>
                                </defs>
                            </svg>
                            <span class="ms-3">Transaction</span>
                        </div>
                        <svg class="dropdown-icon transition-transform" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 10L12 14L17 10" stroke="#28303F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <ul id="transaction-dropdown" class="dropdown-list hidden py-2 space-y-2">
                        <li>
                            <a href="orderConfirmation.php" class="flex items-center p-2 pl-11 rounded-lg text-gray-900 dark:hover:bg-[#FFEEDB] <?php echo $currentPage === 'orderConfirmation.php' ? 'bg-[#FFEEDB] font-semibold text-black border-l-4 border-black rounded-l-none' : 'text-gray-900 dark:hover:bg-[#FFEEDB]'; ?>">
                                Order Confirmation
                            </a>
                        </li>
                        <li>
                            <a href="orderStatusCustomer.php" class="flex items-center p-2 pl-11 rounded-lg text-gray-900 dark:hover:bg-[#FFEEDB] <?php echo $currentPage === 'order.php' ? 'bg-[#FFEEDB] font-semibold text-black border-l-4 border-black rounded-l-none' : 'text-gray-900 dark:hover:bg-[#FFEEDB]'; ?>">
                                Order Status
                            </a>
                        </li>
                        <li>
                            <a href="historyOrder.php" class="flex items-center p-2 pl-11 rounded-lg text-gray-900 dark:hover:bg-[#FFEEDB] <?php echo $currentPage === 'historyOrder.php' ? 'bg-[#FFEEDB] font-semibold text-black border-l-4 border-black rounded-l-none' : 'text-gray-900 dark:hover:bg-[#FFEEDB]'; ?>">
                                History Order
                            </a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="report.php" class="flex items-center p-2 rounded-lg group <?php echo $currentPage === 'report.php' ? 'bg-[#FFEEDB] font-semibold text-black border-l-4 border-black rounded-l-none' : 'text-gray-900 dark:hover:bg-[#FFEEDB]'; ?>">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 10H16M8 14H16M8 18H12M8 4C8 5.10457 8.89543 6 10 6H14C15.1046 6 16 5.10457 16 4M8 4C8 2.89543 8.89543 2 10 2H14C15.1046 2 16 2.89543 16 4M8 4H7C4.79086 4 3 5.79086 3 8V18C3 20.2091 4.79086 22 7 22H17C19.2091 22 21 20.2091 21 18V8C21 5.79086 19.2091 4 17 4H16" stroke="#16151C" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                        <span class="ms-3">Report</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('button[data-dropdown-target]').forEach(btn => {
                btn.addEventListener('click', e => {
                    e.stopPropagation();
                    const id = btn.dataset.dropdownTarget;
                    const menu = document.getElementById(id);
                    const icon = btn.querySelector('.dropdown-icon');

                    menu.classList.toggle('hidden');
                    icon.classList.toggle('rotate-180');

                    document.querySelectorAll('.dropdown-list').forEach(other => {
                        if (other.id !== id && !other.classList.contains('hidden')) {
                            other.classList.add('hidden');
                            const otherBtn = document.querySelector(`[data-dropdown-target="${other.id}"]`);
                            otherBtn.querySelector('.dropdown-icon').classList.remove('rotate-180');
                        }
                    });
                });
            });

            document.addEventListener('click', () => {
                document.querySelectorAll('.dropdown-list').forEach(menu => {
                    if (!menu.classList.contains('hidden')) {
                        menu.classList.add('hidden');
                        const btn = document.querySelector(`[data-dropdown-target="${menu.id}"]`);
                        btn.querySelector('.dropdown-icon').classList.remove('rotate-180');
                    }
                });
            });
        });
    </script>
</body>

</html>
