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
        </style>

    </head>

    <body>
        <!-- drawer component -->
        <div
            class="fixed top-0 left-0 z-40 w-64 h-screen p-4 overflow-y-auto bg-[#E1CDB1]"
            tabindex="-1">
            <img src="../../assets/gambar/logo.png" alt="logoSimaBer" class="mix-blend-multiply mx-0 w-24 h-24">
            <div class="py-4 overflow-y-auto">
                <ul class="space-y-2 font-sm">
                    <li>
                        <a href="dashboardSupplier.php" class="flex items-center p-2 rounded-lg group
                            <?php echo $currentPage === 'dashboardSupplier.php' 
                                ? 'bg-[#FFEEDB] font-semibold text-black border-l-4 border-black rounded-l-none' 
                                : 'text-gray-900 hover:bg-gray-100 dark:hover:bg-[#FFEEDB]'; ?>">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M4 2C2.89543 2 2 2.89543 2 4V8C2 9.10457 2.89543 10 4 10H8C9.10457 10 10 9.10457 10 8V4C10 2.89543 9.10457 2 8 2H4ZM18 10C20.2091 10 22 8.20914 22 6C22 3.79086 20.2091 2 18 2C15.7909 2 14 3.79086 14 6C14 8.20914 15.7909 10 18 10ZM10 18C10 20.2091 8.20914 22 6 22C3.79086 22 2 20.2091 2 18C2 15.7909 3.79086 14 6 14C8.20914 14 10 15.7909 10 18ZM16 14C14.8954 14 14 14.8954 14 16V20C14 21.1046 14.8954 22 16 22H20C21.1046 22 22 21.1046 22 20V16C22 14.8954 21.1046 14 20 14H16Z"
                                    fill="black" />
                            </svg>
                            <span class="ms-3">Dashboard</span>
                        </a>
                    </li>

                    <li>
                        <a href="riceManagement.php" class="flex items-center p-2 rounded-lg group
                            <?php echo $currentPage === 'riceManagement.php' 
                                ? 'bg-[#FFEEDB] font-semibold text-black border-l-4 border-black rounded-l-none' 
                                : 'text-gray-900 hover:bg-gray-100 dark:hover:bg-[#FFEEDB]'; ?>">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 6L9 7C9 8.65685 10.3431 10 12 10C13.6569 10 15 8.65685 15 7V6" stroke="#28303F" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M15.6116 3H8.3886C6.43325 3 4.76449 4.41365 4.44303 6.3424L2.77636 16.3424C2.37001 18.7805 4.25018 21 6.72194 21H17.2783C19.75 21 21.6302 18.7805 21.2238 16.3424L19.5572 6.3424C19.2357 4.41365 17.5669 3 15.6116 3Z" stroke="#28303F" stroke-width="1.3" stroke-linejoin="round" />
                                </svg>
                            <span class="ms-3">Rice Management</span>
                        </a>
                    </li>
                    

                    <li>
                        <a href="orderConfirmation.php" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-black hover:bg-gray-100 dark:hover:bg-[#FFEEDB] group
                            <?php echo $currentPage === 'orderConfirmation.php' 
                                ? 'bg-[#FFEEDB] font-semibold text-black border-l-4 border-black rounded-l-none' 
                                : 'text-gray-900 hover:bg-gray-100 dark:hover:bg-[#FFEEDB]'; ?>">
                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M1.58683 9H20.4132M7 13H15M8 17H14M8 1L5 5M14 1L17 5M17.0351 5L4.96486 5C2.45403 5 0.575939 7.32624 1.08312 9.808L2.71804 17.808C3.09787 19.6666 4.71942 21 6.59978 21H15.4002C17.2806 21 18.9021 19.6666 19.282 17.808L20.9169 9.80799C21.4241 7.32624 19.546 5 17.0351 5Z"
                                    stroke="#28303F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>

                            <span class="flex-1 ms-3 whitespace-nowrap">Order Confirmation</span>
                        </a>
                    </li>
                    <li>
                        <a href="orderStatusSupplier.php" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-black hover:bg-gray-100 dark:hover:bg-[#FFEEDB] group
                            <?php echo $currentPage === 'orderStatusSupplier.php' 
                                ? 'bg-[#FFEEDB] font-semibold text-black border-l-4 border-black rounded-l-none' 
                                : 'text-gray-900 hover:bg-gray-100 dark:hover:bg-[#FFEEDB]'; ?>">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M14 19V7M14 19H16M14 19H9M14 7C14 4.79086 12.2091 3 10 3H6C3.79086 3 2 4.79086 2 7V15C2 16.8652 3.27667 18.4323 5.00384 18.875M14 7H17.2091C17.7172 7 18.2063 7.1934 18.577 7.54093L21.3679 10.1574C21.7712 10.5355 22 11.0636 22 11.6165V17C22 18.1046 21.1046 19 20 19M20 19C20 20.1046 19.1046 21 18 21C16.8954 21 16 20.1046 16 19M20 19C20 17.8954 19.1046 17 18 17C16.8954 17 16 17.8954 16 19M9 19C9 20.1046 8.10457 21 7 21C5.89543 21 5 20.1046 5 19C5 18.958 5.00129 18.9163 5.00384 18.875M9 19C9 17.8954 8.10457 17 7 17C5.93742 17 5.06838 17.8286 5.00384 18.875"
                                    stroke="#28303F" stroke-width="1.5" />
                                <path d="M10 8L8 8" stroke="#28303F" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M10 12L6 12" stroke="#28303F" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>

                            <span class="flex-1 ms-3 whitespace-nowrap">Order Status</span>
                        </a>
                    </li>
                    <li>
                        <a href="orderHistorySupplier.php" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-black hover:bg-gray-100 dark:hover:bg-[#FFEEDB] group
                            <?php echo $currentPage === 'orderHistorySupplier.php' 
                                ? 'bg-[#FFEEDB] font-semibold text-black border-l-4 border-black rounded-l-none' 
                                : 'text-gray-900 hover:bg-gray-100 dark:hover:bg-[#FFEEDB]'; ?>">
                            <svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M2.82934 2.64687C6.62434 -0.911682 12.7943 -0.874125 16.6123 2.71165C20.4323 6.29837 20.4723 12.0944 16.6763 15.6576C12.8803 19.2209 6.70834 19.1843 2.88834 15.5975C1.81371 14.5929 1.00177 13.368 0.516293 12.0191C0.0308133 10.6702 -0.114988 9.23394 0.0903395 7.82319C0.117259 7.63805 0.221409 7.47053 0.379877 7.35748C0.538345 7.24443 0.738151 7.19513 0.93534 7.2204C1.13253 7.24568 1.31095 7.34347 1.43134 7.49226C1.55174 7.64105 1.60426 7.82865 1.57734 8.0138C1.40302 9.20935 1.52642 10.4266 1.93782 11.5698C2.34923 12.713 3.03744 13.751 3.94834 14.6023C7.19334 17.6482 12.4163 17.666 15.6163 14.6624C18.8153 11.6578 18.7963 6.75375 15.5523 3.70692C12.3093 0.662905 7.08934 0.643188 3.88934 3.64307L4.63734 3.64589C4.73583 3.64632 4.83327 3.66496 4.92409 3.70075C5.0149 3.73654 5.09733 3.78877 5.16664 3.85447C5.23596 3.92016 5.29082 3.99804 5.32809 4.08364C5.36536 4.16924 5.3843 4.2609 5.38384 4.35337C5.38338 4.44585 5.36353 4.53734 5.32541 4.62261C5.28729 4.70788 5.23166 4.78527 5.16169 4.85036C5.09173 4.91544 5.00879 4.96695 4.91762 5.00194C4.82645 5.03693 4.72883 5.05472 4.63034 5.05429L2.08434 5.04302C1.88664 5.04203 1.69735 4.96779 1.55765 4.83644C1.41795 4.70509 1.33913 4.52727 1.33834 4.34164L1.32534 1.953C1.32481 1.86052 1.34369 1.76885 1.3809 1.68323C1.4181 1.5976 1.47291 1.51969 1.54218 1.45395C1.61145 1.38821 1.69384 1.33593 1.78463 1.30008C1.87542 1.26424 1.97285 1.24554 2.07134 1.24504C2.16983 1.24455 2.26746 1.26228 2.35866 1.29721C2.44985 1.33214 2.53283 1.3836 2.60284 1.44864C2.67286 1.51368 2.72854 1.59104 2.76672 1.67629C2.80489 1.76153 2.82481 1.85301 2.82534 1.94549L2.82934 2.64687ZM9.74934 4.69374C9.94825 4.69374 10.139 4.76793 10.2797 4.89999C10.4203 5.03205 10.4993 5.21117 10.4993 5.39794V8.86259L12.7803 11.0034C12.852 11.0683 12.9092 11.146 12.9485 11.2319C12.9879 11.3178 13.0086 11.4102 13.0095 11.5037C13.0104 11.5972 12.9915 11.6899 12.9538 11.7765C12.9162 11.863 12.8605 11.9417 12.7901 12.0078C12.7198 12.074 12.636 12.1263 12.5439 12.1617C12.4517 12.1972 12.353 12.215 12.2534 12.2143C12.1538 12.2135 12.0554 12.1941 11.9639 12.1573C11.8723 12.1204 11.7896 12.0668 11.7203 11.9996L9.00034 9.44567V5.39887C9.00034 5.21211 9.07936 5.03299 9.22001 4.90093C9.36066 4.76887 9.55143 4.69468 9.75034 4.69468"
                                    fill="black" />
                            </svg>

                            <span class="flex-1 ms-3 whitespace-nowrap">Order History</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>



    </body>

    <script>
    const drawer = document.getElementById("drawer-navigation");
    const hamburgerBtn = document.getElementById("hamburgerBtn");

    const observer = new MutationObserver(() => {
        const isOpen = !drawer.classList.contains("-translate-x-full");
        hamburgerBtn.classList.toggle("hidden", isOpen);
    });

    observer.observe(drawer, {
        attributes: true,
        attributeFilter: ['class']
    });

    new Drawer(document.getElementById('drawer-navigation'), {
        backdrop: false
    });
    </script>

    </html>