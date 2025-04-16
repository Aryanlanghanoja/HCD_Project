document.addEventListener('DOMContentLoaded', function() {
    // Make pagination interactive
    const pageItems = document.querySelectorAll('.page-item');
    
    pageItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active class from all page items
            pageItems.forEach(pi => pi.classList.remove('active'));
            
            // Add active class to clicked item if it's a number
            if (!['«', '»'].includes(this.textContent)) {
                this.classList.add('active');
            }
            
            // In a real application, you would fetch data for the selected page here
            console.log('Page selected:', this.textContent);
        });
    });
    
    // Search functionality
    const searchInput = document.querySelector('.search-input input');
    const searchButton = document.querySelector('.search-input button');
    
    searchButton.addEventListener('click', function() {
        const searchQuery = searchInput.value.trim();
        if (searchQuery) {
            console.log('Searching for:', searchQuery);
            // In a real application, you would perform search here
        }
    });
    
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            const searchQuery = this.value.trim();
            if (searchQuery) {
                console.log('Searching for:', searchQuery);
                // In a real application, you would perform search here
            }
        }
    });
    
    // Filter functionality
    const filterSelects = document.querySelectorAll('.filter-select');
    
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            const filters = {};
            
            filterSelects.forEach(fs => {
                if (fs.value) {
                    filters[fs.querySelector('option:checked').parentElement.label || 'sort'] = fs.value;
                }
            });
            
            console.log('Filters applied:', filters);
            // In a real application, you would filter data based on selected filters
        });
    });
});