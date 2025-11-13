const menuIcon = document.getElementById('menu-icon');
    const sideMenu = document.getElementById('side-menu');

    menuIcon.addEventListener('click', () => {
      sideMenu.classList.toggle('active');
    });

const fadeElements = document.querySelectorAll('.fade-in-up');
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) entry.target.classList.add('visible');
      });
    }, { threshold: 0.2 });
    fadeElements.forEach(el => observer.observe(el));