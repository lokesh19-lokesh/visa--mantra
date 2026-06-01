document.addEventListener('DOMContentLoaded', () => {
    // 0. Full-screen preloader splash screen
    const preloader = document.getElementById('preloader');
    if (preloader && preloader.style.display !== 'none') {
        setTimeout(() => {
            preloader.classList.add('fade-out');
            document.body.classList.remove('preloader-active');
        }, 2000);
    }

    // 1. Navbar Scroll Effect
    const navbar = document.querySelector('.navbar-glass');
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    // 2. Slide-out Inquiry Drawer Triggers
    const openDrawerBtn = document.getElementById('openDrawerBtn');
    const drawerOverlay = document.getElementById('drawerOverlay');
    const drawerContent = document.getElementById('drawerContent');
    const closeDrawerBtn = document.getElementById('closeDrawerBtn');

    const toggleDrawer = (state) => {
        if (state === 'open') {
            drawerOverlay.classList.add('active');
            drawerContent.classList.add('active');
            document.body.style.overflow = 'hidden';
        } else {
            drawerOverlay.classList.remove('active');
            drawerContent.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    };

    if (openDrawerBtn && drawerOverlay && drawerContent && closeDrawerBtn) {
        openDrawerBtn.addEventListener('click', () => toggleDrawer('open'));
        closeDrawerBtn.addEventListener('click', () => toggleDrawer('close'));
        drawerOverlay.addEventListener('click', () => toggleDrawer('close'));
    }

    // Connect form links if they exist
    const triggerClassElements = document.querySelectorAll('.trigger-drawer-form');
    triggerClassElements.forEach(element => {
        element.addEventListener('click', (e) => {
            e.preventDefault();
            toggleDrawer('open');
        });
    });

    // 3. Stats Counter Animation
    const statsSection = document.getElementById('statsSection');
    const statNumbers = document.querySelectorAll('.stat-number');
    let animated = false;

    const animateCounters = () => {
        statNumbers.forEach(stat => {
            const target = parseInt(stat.getAttribute('data-target'), 10);
            let count = 0;
            const speed = 2000 / target; // complete in 2 seconds

            const updateCount = () => {
                count += Math.ceil(target / 100);
                if (count >= target) {
                    stat.innerText = target + '+';
                } else {
                    stat.innerText = count + '+';
                    setTimeout(updateCount, speed);
                }
            };
            updateCount();
        });
    };

    if (statsSection && statNumbers.length > 0) {
        const observerOptions = {
            root: null,
            threshold: 0.3
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !animated) {
                    animateCounters();
                    animated = true;
                }
            });
        }, observerOptions);

        observer.observe(statsSection);
    }

    // 4. Jobs Client-Side Filter
    const filterButtons = document.querySelectorAll('.filter-btn');
    const jobItems = document.querySelectorAll('.job-item-card');

    if (filterButtons.length > 0 && jobItems.length > 0) {
        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active from all
                filterButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');

                const filterValue = button.getAttribute('data-filter');

                jobItems.forEach(item => {
                    const country = item.getAttribute('data-country');
                    const sector = item.getAttribute('data-sector');

                    if (filterValue === 'all' || country === filterValue || sector === filterValue) {
                        item.style.display = 'block';
                        // Add fade-in animation trigger
                        item.classList.add('animate__fadeIn');
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    }

    // 5. Fill application modal job title
    const applyModal = document.getElementById('applyJobModal');
    if (applyModal) {
        applyModal.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget;
            const jobTitle = button.getAttribute('data-job-title');
            const jobDestination = button.getAttribute('data-job-destination');
            
            const modalTitleInput = applyModal.querySelector('#modalJobTitle');
            const modalDestInput = applyModal.querySelector('#modalJobDestination');
            
            if (modalTitleInput && jobTitle) {
                modalTitleInput.value = jobTitle;
            }
            if (modalDestInput && jobDestination) {
                modalDestInput.value = jobDestination;
            }
        });
    }

    // 6. The Bubbles Media Floating Hover Animation
    const bubbleLinks = document.querySelectorAll('.bubbles-hover-link');
    bubbleLinks.forEach(link => {
        let intervalId = null;
        
        const createBubble = () => {
            const bubble = document.createElement('span');
            bubble.classList.add('bubble-effect');
            
            const size = Math.random() * 8 + 4;
            bubble.style.width = `${size}px`;
            bubble.style.height = `${size}px`;
            
            const leftOffset = Math.random() * link.offsetWidth;
            bubble.style.left = `${leftOffset}px`;
            
            const drift = (Math.random() * 30 - 15);
            bubble.style.setProperty('--bubble-drift', `${drift}px`);
            
            link.appendChild(bubble);
            
            setTimeout(() => {
                bubble.remove();
            }, 1200);
        };

        link.addEventListener('mouseenter', () => {
            createBubble();
            intervalId = setInterval(createBubble, 120);
        });

        link.addEventListener('mouseleave', () => {
            if (intervalId) {
                clearInterval(intervalId);
                intervalId = null;
            }
        });
    });
});
