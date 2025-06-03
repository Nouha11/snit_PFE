document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar when button is clicked
    const sidebarCollapseBtn = document.getElementById('sidebarCollapseBtn');
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    
    if (sidebarCollapseBtn) {
        sidebarCollapseBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('expanded');
            
            // Change the icon direction when toggled
            const icon = sidebarCollapseBtn.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.classList.remove('bi-chevron-left');
                icon.classList.add('bi-chevron-right');
            } else {
                icon.classList.remove('bi-chevron-right');
                icon.classList.add('bi-chevron-left');
            }
        });
    }
    
    // Add active class to current page link
    const currentLocation = window.location.href;
    const menuItems = document.querySelectorAll('.sidebar-link');
    menuItems.forEach(item => {
        if (item.href === currentLocation) {
            item.classList.add('active');
        }
    });
});