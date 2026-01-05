            </div><!-- End admin-content -->
        </div><!-- End admin-main -->
    </div><!-- End admin-container -->

    <script>
        // Mobile sidebar toggle
        const toggleBtn = document.createElement('button');
        toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
        toggleBtn.className = 'mobile-toggle';
        toggleBtn.style.cssText = `
            position: fixed;
            top: 20px;
            left: 20px;
            background: var(--primary-color);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            display: none;
        `;
        
        document.body.appendChild(toggleBtn);
        
        toggleBtn.addEventListener('click', function() {
            document.getElementById('adminSidebar').classList.toggle('active');
        });
        
        // Show toggle button on mobile
        function checkMobile() {
            if (window.innerWidth <= 768) {
                toggleBtn.style.display = 'flex';
            } else {
                toggleBtn.style.display = 'none';
                document.getElementById('adminSidebar').classList.remove('active');
            }
        }
        
        checkMobile();
        window.addEventListener('resize', checkMobile);
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('adminSidebar');
            const toggleBtn = document.querySelector('.mobile-toggle');
            
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && event.target !== toggleBtn && !toggleBtn.contains(event.target) && sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                }
            }
        });
        
        // Auto-hide sidebar on mobile when clicking a link
        document.querySelectorAll('.nav-item').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    document.getElementById('adminSidebar').classList.remove('active');
                }
            });
        });
        
        // Confirmation for delete actions
        document.addEventListener('submit', function(e) {
            if (e.target.classList.contains('delete-form')) {
                if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                    e.preventDefault();
                }
            }
        });
        
        // Flash message auto-hide
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
        
        // Search functionality helper
        function filterTable(inputId, tableId) {
            const input = document.getElementById(inputId);
            const table = document.getElementById(tableId);
            
            if (!input || !table) return;
            
            const rows = table.getElementsByTagName('tr');
            
            input.addEventListener('keyup', function() {
                const filter = this.value.toLowerCase();
                
                for (let i = 1; i < rows.length; i++) {
                    const cells = rows[i].getElementsByTagName('td');
                    let found = false;
                    
                    for (let j = 0; j < cells.length; j++) {
                        if (cells[j].textContent.toLowerCase().includes(filter)) {
                            found = true;
                            break;
                        }
                    }
                    
                    rows[i].style.display = found ? '' : 'none';
                }
            });
        }
        
        // Initialize tooltips
        const tooltipElements = document.querySelectorAll('[title]');
        tooltipElements.forEach(el => {
            el.addEventListener('mouseenter', function(e) {
                const tooltip = document.createElement('div');
                tooltip.className = 'custom-tooltip';
                tooltip.textContent = this.title;
                tooltip.style.cssText = `
                    position: fixed;
                    background: rgba(78, 73, 52, 0.9);
                    color: #dbd1c8;
                    padding: 5px 10px;
                    border-radius: 4px;
                    font-size: 12px;
                    z-index: 9999;
                    pointer-events: none;
                    white-space: nowrap;
                    font-family: 'Poppins', sans-serif;
                `;
                
                document.body.appendChild(tooltip);
                
                const rect = this.getBoundingClientRect();
                tooltip.style.left = (rect.left + rect.width / 2 - tooltip.offsetWidth / 2) + 'px';
                tooltip.style.top = (rect.top - tooltip.offsetHeight - 10) + 'px';
                
                this._tooltip = tooltip;
            });
            
            el.addEventListener('mouseleave', function() {
                if (this._tooltip) {
                    this._tooltip.remove();
                    delete this._tooltip;
                }
            });
        });
        
        // Prevent form resubmission on refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
        
        // Auto-refresh dashboard every 60 seconds
        if (window.location.pathname.includes('admin_dashboard.php')) {
            setInterval(() => {
                if (!document.hidden) {
                    window.location.reload();
                }
            }, 60000);
        }
        
        // Format currency inputs
        document.querySelectorAll('input[type="number"].currency').forEach(input => {
            input.addEventListener('blur', function() {
                const value = parseFloat(this.value);
                if (!isNaN(value)) {
                    this.value = value.toFixed(2);
                }
            });
        });
        
        // Validate email inputs
        document.querySelectorAll('input[type="email"]').forEach(input => {
            input.addEventListener('blur', function() {
                const email = this.value;
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (email && !emailRegex.test(email)) {
                    this.style.borderColor = 'var(--danger-color)';
                    this.nextElementSibling?.remove();
                    const error = document.createElement('div');
                    error.className = 'error-message';
                    error.style.cssText = 'color: var(--danger-color); font-size: 12px; margin-top: 5px;';
                    error.textContent = 'Please enter a valid email address';
                    this.parentNode.appendChild(error);
                } else {
                    this.style.borderColor = '';
                    this.nextElementSibling?.remove();
                }
            });
        });
    </script>
</body>
</html>