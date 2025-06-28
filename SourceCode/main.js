
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('content');
        const navbar = document.getElementById('top-navbar');
        
        const searchInput = document.getElementById('sidebarSearch');
        const menuItems = document.querySelectorAll('.sidebar-nav a');


        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('full');
            navbar.classList.toggle('collapsed'); 
        });


        searchInput.addEventListener('input', () => {
            const keyword = searchInput.value.toLowerCase();
            menuItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(keyword) ? 'flex' : 'none';
            });
        });
