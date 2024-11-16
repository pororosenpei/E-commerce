// Initialize Lucide icons
lucide.createIcons();

// Trending sidebar functionality
const trendingSidebar = document.getElementById('trending-sidebar');
const trendingToggle = document.getElementById('trending-toggle');

trendingToggle.addEventListener('click', function() {
    trendingSidebar.classList.toggle('open');
});

// Close trending sidebar when clicking outside
document.addEventListener('click', function(event) {
    if (!trendingSidebar.contains(event.target) && event.target !== trendingToggle) {
        trendingSidebar.classList.remove('open');
    }
});

// Prevent closing when clicking inside the trending sidebar
trendingSidebar.addEventListener('click', function(event) {
    event.stopPropagation();
});

// Navigation item active state
const navItems = document.querySelectorAll('.nav-item');
navItems.forEach(item => {
    item.addEventListener('click', function() {
        navItems.forEach(i => i.classList.remove('active'));
        this.classList.add('active');
    });
});

// Stagger animation for dashboard content
const dashboardItems = document.querySelectorAll('.dashboard-content > *');
dashboardItems.forEach((item, index) => {
    item.style.animationDelay = `${index * 0.1}s`;
});
