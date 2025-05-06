<?php
$title = "TRA Receipt Generator System Documentation";
$subtitle = "For Educational Purposes Only";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .disclaimer-banner {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
        }
        .feature-card {
            transition: all 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .nav-link {
            position: relative;
        }
        .nav-link:hover::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: #3b82f6;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Header -->
        <header class="mb-12 text-center">
            <h1 class="text-4xl font-bold text-gray-800 mb-2"><?php echo $title; ?></h1>
            <h2 class="text-2xl font-semibold text-gray-600"><?php echo $subtitle; ?></h2>
            <div class="disclaimer-banner p-4 rounded-md mt-6 text-left">
                <p class="font-bold text-yellow-800">⚠️ Important Disclaimer:</p>
                <p class="text-yellow-700">This system is developed <strong>solely for educational purposes</strong> to demonstrate web development concepts. It <strong>must not</strong> be used for any illegal activities, including but not limited to tax evasion, fraud, or falsifying official documents. The developer assumes <strong>no responsibility</strong> for any misuse of this system. Use at your own risk.</p>
            </div>
        </header>

        <!-- Table of Contents -->
        <section class="mb-12 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">Table of Contents</h2>
            <ul class="space-y-2">
                <li><a href="#system-overview" class="nav-link text-blue-600 hover:text-blue-800">1. System Overview</a></li>
                <li><a href="#installation-guide" class="nav-link text-blue-600 hover:text-blue-800">2. Installation Guide</a></li>
                <li><a href="#running-the-system" class="nav-link text-blue-600 hover:text-blue-800">3. Running the System</a></li>
                <li><a href="#features" class="nav-link text-blue-600 hover:text-blue-800">4. Features</a></li>
                <li><a href="#database-structure" class="nav-link text-blue-600 hover:text-blue-800">5. Database Structure</a></li>
                <li><a href="#legal-ethical-considerations" class="nav-link text-blue-600 hover:text-blue-800">6. Legal & Ethical Considerations</a></li>
                <li><a href="#troubleshooting" class="nav-link text-blue-600 hover:text-blue-800">7. Troubleshooting</a></li>
            </ul>
        </section>

        <!-- System Overview -->
        <section id="system-overview" class="mb-12 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">1. System Overview</h2>
            <p class="mb-4 text-gray-700">This is a <strong>PHP-based web application</strong> that simulates a receipt generation system for educational purposes. It includes:</p>
            
            <ul class="list-disc pl-6 mb-6 space-y-2 text-gray-700">
                <li><strong>Database integration</strong> (MySQL)</li>
                <li><strong>Dynamic receipt generation</strong> with TRA branding</li>
                <li><strong>QR code verification</strong></li>
                <li><strong>PDF/Image export</strong></li>
                <li><strong>Responsive Tailwind CSS UI</strong></li>
            </ul>

            <h3 class="text-xl font-semibold text-gray-700 mb-2">Tech Stack:</h3>
            <div class="flex flex-wrap gap-2 mb-4">
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">PHP 8.0+</span>
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">MySQL</span>
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">Tailwind CSS</span>
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">HTML2Canvas</span>
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">QRCode.js</span>
            </div>
        </section>

        <!-- Installation Guide -->
        <section id="installation-guide" class="mb-12 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">2. Installation Guide</h2>
            
            <h3 class="text-xl font-semibold text-gray-700 mb-3">Prerequisites</h3>
            <ul class="list-disc pl-6 mb-6 space-y-2 text-gray-700">
                <li><strong>Web Server</strong> (Apache/Nginx)</li>
                <li><strong>PHP 8.0+</strong></li>
                <li><strong>MySQL 5.7+</strong></li>
                <li><strong>Composer</strong> (for dependency management)</li>
            </ul>

            <h3 class="text-xl font-semibold text-gray-700 mb-3">Step-by-Step Setup</h3>
            
            <div class="bg-gray-100 p-4 rounded-md mb-4">
                <h4 class="font-semibold text-gray-800 mb-2">1. Clone the Repository</h4>
                <pre class="bg-gray-800 text-gray-100 p-3 rounded overflow-x-auto"><code>git clone https://github.com/your-repo/tra-receipt-generator.git
cd tra-receipt-generator</code></pre>
            </div>
            
            <div class="bg-gray-100 p-4 rounded-md mb-4">
                <h4 class="font-semibold text-gray-800 mb-2">2. Configure Database</h4>
                <p class="mb-2 text-gray-700">Create a MySQL database (e.g., <code>receipt_db</code>).</p>
                <p class="text-gray-700">Update <code>DB_HOST</code>, <code>DB_USER</code>, <code>DB_PASS</code>, and <code>DB_NAME</code> in the PHP file.</p>
            </div>
            
            <div class="bg-gray-100 p-4 rounded-md mb-4">
                <h4 class="font-semibold text-gray-800 mb-2">3. Set Up the Database Schema</h4>
                <p class="text-gray-700">The system will automatically create the required tables on first run.</p>
            </div>
            
            <div class="bg-gray-100 p-4 rounded-md mb-4">
                <h4 class="font-semibold text-gray-800 mb-2">4. Place TRA Logo</h4>
                <p class="text-gray-700">Ensure <code>tralogo.png</code> is in the project root directory.</p>
            </div>
            
            <div class="bg-gray-100 p-4 rounded-md">
                <h4 class="font-semibold text-gray-800 mb-2">5. Run the Application</h4>
                <pre class="bg-gray-800 text-gray-100 p-3 rounded overflow-x-auto"><code>php -S localhost:8000</code></pre>
                <p class="mt-2 text-gray-700">Access via: <a href="http://localhost:8000" class="text-blue-600 hover:underline">http://localhost:8000</a></p>
            </div>
        </section>

        <!-- Running the System -->
        <section id="running-the-system" class="mb-12 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">3. Running the System</h2>
            
            <h3 class="text-xl font-semibold text-gray-700 mb-3">Generating a Receipt</h3>
            <ol class="list-decimal pl-6 mb-6 space-y-3 text-gray-700">
                <li>Fill in <strong>Shop Details</strong> (Name, TIN, Address, etc.)</li>
                <li>Add <strong>Items</strong> (Name, Price, Tax Status)</li>
                <li>Select <strong>Payment Method</strong> (Cash/Credit Card)</li>
                <li>Click <strong>"Generate Receipt"</strong></li>
            </ol>
            
            <h3 class="text-xl font-semibold text-gray-700 mb-3">Export Options</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-gray-100 p-4 rounded-md">
                    <p class="font-semibold text-gray-800">• Print</p>
                    <p class="text-gray-700">(Browser Print Dialog)</p>
                </div>
                <div class="bg-gray-100 p-4 rounded-md">
                    <p class="font-semibold text-gray-800">• Download PDF</p>
                    <p class="text-gray-700">(High-resolution PDF)</p>
                </div>
                <div class="bg-gray-100 p-4 rounded-md">
                    <p class="font-semibold text-gray-800">• Download JPG</p>
                    <p class="text-gray-700">(Image format)</p>
                </div>
            </div>
            
            <h3 class="text-xl font-semibold text-gray-700 mb-3">QR Code Verification</h3>
            <p class="text-gray-700">Each receipt generates a <strong>unique verification code</strong> embedded in a QR code.</p>
        </section>

        <!-- Features -->
        <section id="features" class="mb-12 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">4. Features</h2>
            
            <h3 class="text-xl font-semibold text-gray-700 mb-3">Core Features</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="feature-card bg-gray-50 p-4 rounded-md border border-gray-200">
                    <p class="font-semibold text-green-700 mb-2">✅ Dynamic Receipt Generation</p>
                    <p class="text-gray-700">Customizable fields for shop information and items.</p>
                </div>
                <div class="feature-card bg-gray-50 p-4 rounded-md border border-gray-200">
                    <p class="font-semibold text-green-700 mb-2">✅ Tax Calculation</p>
                    <p class="text-gray-700">Supports 18% VAT (configurable).</p>
                </div>
                <div class="feature-card bg-gray-50 p-4 rounded-md border border-gray-200">
                    <p class="font-semibold text-green-700 mb-2">✅ Database Storage</p>
                    <p class="text-gray-700">All receipts are saved in MySQL.</p>
                </div>
                <div class="feature-card bg-gray-50 p-4 rounded-md border border-gray-200">
                    <p class="font-semibold text-green-700 mb-2">✅ Responsive UI</p>
                    <p class="text-gray-700">Works on desktop & mobile.</p>
                </div>
                <div class="feature-card bg-gray-50 p-4 rounded-md border border-gray-200">
                    <p class="font-semibold text-green-700 mb-2">✅ Export Options</p>
                    <p class="text-gray-700">Print, PDF, JPG formats available.</p>
                </div>
            </div>
            
            <h3 class="text-xl font-semibold text-gray-700 mb-3">Security Notes</h3>
            <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-md">
                <p class="font-semibold text-yellow-800 mb-2">🔒 No real financial data is processed.</p>
                <p class="font-semibold text-yellow-800">🔒 No connection to actual TRA systems.</p>
            </div>
        </section>

        <!-- Database Structure -->
        <section id="database-structure" class="mb-12 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">5. Database Structure</h2>
            
            <h3 class="text-xl font-semibold text-gray-700 mb-3">Tables</h3>
            <h4 class="font-semibold text-gray-800 mb-2">receipts</h4>
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-2 px-4 border-b border-gray-200 text-left text-gray-700">Column</th>
                            <th class="py-2 px-4 border-b border-gray-200 text-left text-gray-700">Type</th>
                            <th class="py-2 px-4 border-b border-gray-200 text-left text-gray-700">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="py-2 px-4 border-b border-gray-200"><code>id</code></td>
                            <td class="py-2 px-4 border-b border-gray-200">INT</td>
                            <td class="py-2 px-4 border-b border-gray-200">Auto-incremented ID</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-4 border-b border-gray-200"><code>shop_name</code></td>
                            <td class="py-2 px-4 border-b border-gray-200">VARCHAR(255)</td>
                            <td class="py-2 px-4 border-b border-gray-200">Business name</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-4 border-b border-gray-200"><code>address</code></td>
                            <td class="py-2 px-4 border-b border-gray-200">VARCHAR(255)</td>
                            <td class="py-2 px-4 border-b border-gray-200">Business address</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-4 border-b border-gray-200"><code>tin</code></td>
                            <td class="py-2 px-4 border-b border-gray-200">VARCHAR(50)</td>
                            <td class="py-2 px-4 border-b border-gray-200">Taxpayer Identification Number</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-4 border-b border-gray-200"><code>receipt_number</code></td>
                            <td class="py-2 px-4 border-b border-gray-200">VARCHAR(50)</td>
                            <td class="py-2 px-4 border-b border-gray-200">Unique receipt ID</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-4 border-b border-gray-200"><code>items</code></td>
                            <td class="py-2 px-4 border-b border-gray-200">JSON</td>
                            <td class="py-2 px-4 border-b border-gray-200">List of purchased items</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-4 border-b border-gray-200"><code>verification_code</code></td>
                            <td class="py-2 px-4 border-b border-gray-200">VARCHAR(50)</td>
                            <td class="py-2 px-4 border-b border-gray-200">Unique 10-digit code</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="mt-2 text-gray-600 text-sm">(See full schema in the code)</p>
        </section>

        <!-- Legal & Ethical Considerations -->
        <section id="legal-ethical-considerations" class="mb-12 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">6. Legal & Ethical Considerations</h2>
            
            <h3 class="text-xl font-semibold text-gray-700 mb-3">⚠️ Strictly Educational Use</h3>
            <p class="mb-4 text-gray-700">This system is a <strong>simulation</strong> for learning:</p>
            <ul class="list-disc pl-6 mb-6 space-y-2 text-gray-700">
                <li><strong>Not for real-world use.</strong></li>
                <li><strong>Not connected to TRA systems.</strong></li>
                <li><strong>No financial/legal validity.</strong></li>
            </ul>
            
            <h3 class="text-xl font-semibold text-gray-700 mb-3">Developer Liability</h3>
            <p class="mb-4 text-gray-700">The developer <strong>disclaims all responsibility</strong> for misuse. By using this software, you agree:</p>
            <ul class="list-disc pl-6 mb-6 space-y-2 text-gray-700">
                <li><strong>You will not use it for fraud.</strong></li>
                <li><strong>You understand it has no legal validity.</strong></li>
                <li><strong>You accept all risks of misuse.</strong></li>
            </ul>
        </section>

        <!-- Troubleshooting -->
        <section id="troubleshooting" class="mb-12 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">7. Troubleshooting</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-2 px-4 border-b border-gray-200 text-left text-gray-700">Issue</th>
                            <th class="py-2 px-4 border-b border-gray-200 text-left text-gray-700">Solution</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="py-2 px-4 border-b border-gray-200">Database connection fails</td>
                            <td class="py-2 px-4 border-b border-gray-200">Check <code>DB_*</code> credentials in PHP</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-4 border-b border-gray-200">TRA logo not appearing</td>
                            <td class="py-2 px-4 border-b border-gray-200">Ensure <code>tralogo.png</code> is in root</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-4 border-b border-gray-200">PDF export issues</td>
                            <td class="py-2 px-4 border-b border-gray-200">Verify HTML2Canvas is loaded</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-4 border-b border-gray-200">QR code not generating</td>
                            <td class="py-2 px-4 border-b border-gray-200">Check QRCode.js inclusion</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Footer -->
        <footer class="text-center py-6 border-t border-gray-200">
            <p class="text-gray-600 mb-2">This project is <strong>for learning only</strong>. Always comply with local laws and regulations.</p>
            <p class="text-xl font-semibold text-blue-600">🚀 Happy Coding (Ethically)!</p>
        </footer>
    </div>
</body>
</html>