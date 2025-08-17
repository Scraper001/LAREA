<?php include "../includes/header.php" ?>

<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center">
            <i class="fas fa-exclamation-triangle text-red-500 text-6xl mb-4"></i>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Access Denied</h2>
            <p class="text-gray-600 mb-8">You don't have permission to access this page.</p>
            
            <div class="space-y-4">
                <a href="javascript:history.back()" 
                   class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Go Back
                </a>
                
                <a href="../auth/login.php" 
                   class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Login with Different Account
                </a>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php" ?>