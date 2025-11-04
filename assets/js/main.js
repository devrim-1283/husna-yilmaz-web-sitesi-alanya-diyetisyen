/**
 * Main JavaScript
 * Diyetisyen Hüsna Yılmaz
 */

// ===== TOAST NOTIFICATION SYSTEM =====
function showToast(message, type = 'success', title = '') {
    // Create toast container if not exists
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    
    // Set default titles
    const titles = {
        success: title || 'Başarılı!',
        error: title || 'Hata!',
        warning: title || 'Uyarı!'
    };
    
    // Set icons
    const icons = {
        success: '<i class="fas fa-check-circle"></i>',
        error: '<i class="fas fa-times-circle"></i>',
        warning: '<i class="fas fa-exclamation-triangle"></i>'
    };
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <div class="toast-icon">${icons[type]}</div>
        <div class="toast-content">
            <div class="toast-title">${titles[type]}</div>
            <div class="toast-message">${message}</div>
        </div>
        <button class="toast-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Add to container
    container.appendChild(toast);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        toast.classList.add('hiding');
        setTimeout(() => {
            toast.remove();
            // Remove container if empty
            if (container.children.length === 0) {
                container.remove();
            }
        }, 300);
    }, 5000);
}

document.addEventListener('DOMContentLoaded', function() {
    
    // Header scroll effect
    const mainHeader = document.querySelector('.main-header');
    if (mainHeader) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                mainHeader.classList.add('scrolled');
            } else {
                mainHeader.classList.remove('scrolled');
            }
        });
    }
    
    // Scroll-based animations
    const observerOptions = {
        threshold: 0.2,
        rootMargin: '0px 0px -100px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate');
            }
        });
    }, observerOptions);
    
    // Observe all animated elements
    document.querySelectorAll('.fade-in-left, .fade-in-right, .fade-in-up').forEach(element => {
        observer.observe(element);
    });
    
    // Mobile Menu Toggle
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mainNav = document.getElementById('mainNav');
    
    if (mobileMenuToggle && mainNav) {
        mobileMenuToggle.addEventListener('click', function() {
            this.classList.toggle('active');
            const navMenu = mainNav.querySelector('.nav-menu');
            if (navMenu) {
                navMenu.classList.toggle('active');
            }
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!mainNav.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                mobileMenuToggle.classList.remove('active');
                const navMenu = mainNav.querySelector('.nav-menu');
                if (navMenu) {
                    navMenu.classList.remove('active');
                }
            }
        });
    }
    
    // Dropdown Menu Toggle
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        
        if (toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Close other dropdowns
                dropdowns.forEach(other => {
                    if (other !== dropdown) {
                        other.classList.remove('active');
                    }
                });
                
                // Toggle current dropdown
                dropdown.classList.toggle('active');
            });
        }
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('active');
            });
        }
    });
    
    // Smooth Scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
    
    // Lazy Loading Images
    const lazyImages = document.querySelectorAll('img[loading="lazy"]');
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src || img.src;
                    img.classList.add('loaded');
                    observer.unobserve(img);
                }
            });
        });
        
        lazyImages.forEach(img => imageObserver.observe(img));
    }
    
    // Modal Handler
    const modalTriggers = document.querySelectorAll('[data-modal-target]');
    const modalCloses = document.querySelectorAll('.modal-close');
    const modals = document.querySelectorAll('.modal');
    
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.dataset.modalTarget;
            const modal = document.getElementById(targetId);
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        });
    });
    
    modalCloses.forEach(close => {
        close.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });
    
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });
    
    // Form Validation
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = this.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#dc3545';
                    
                    // Remove error style on input
                    field.addEventListener('input', function() {
                        this.style.borderColor = '';
                    }, { once: true });
                }
            });
            
            // Email validation
            const emailFields = this.querySelectorAll('input[type="email"]');
            emailFields.forEach(field => {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (field.value && !emailRegex.test(field.value)) {
                    isValid = false;
                    field.style.borderColor = '#dc3545';
                }
            });
            
            // Phone validation (Turkish format)
            const phoneFields = this.querySelectorAll('input[type="tel"]');
            phoneFields.forEach(field => {
                const phoneRegex = /^(\+90|0)?[0-9]{10}$/;
                if (field.value && !phoneRegex.test(field.value.replace(/\s/g, ''))) {
                    isValid = false;
                    field.style.borderColor = '#dc3545';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Lütfen tüm gerekli alanları doğru şekilde doldurun.');
            }
        });
    });
    
    // Success Stories Slider (if exists)
    const slider = document.querySelector('.stories-slider');
    if (slider) {
        let currentSlide = 0;
        const slides = slider.querySelectorAll('.story-slide');
        const totalSlides = slides.length;
        
        if (totalSlides > 0) {
            const prevBtn = slider.querySelector('.slider-prev');
            const nextBtn = slider.querySelector('.slider-next');
            
            function showSlide(index) {
                slides.forEach(slide => slide.classList.remove('active'));
                slides[index].classList.add('active');
            }
            
            if (prevBtn) {
                prevBtn.addEventListener('click', () => {
                    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
                    showSlide(currentSlide);
                });
            }
            
            if (nextBtn) {
                nextBtn.addEventListener('click', () => {
                    currentSlide = (currentSlide + 1) % totalSlides;
                    showSlide(currentSlide);
                });
            }
            
            // Auto-play
            setInterval(() => {
                currentSlide = (currentSlide + 1) % totalSlides;
                showSlide(currentSlide);
            }, 5000);
            
            // Show first slide
            showSlide(0);
        }
    }
    
    // Scroll to top button
    const scrollTopBtn = document.getElementById('scrollTopBtn');
    if (scrollTopBtn) {
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                scrollTopBtn.style.display = 'flex';
            } else {
                scrollTopBtn.style.display = 'none';
            }
        });
        
        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // Karakter sayacı (index.php için)
    const messageField = document.getElementById('message');
    const charCount = document.getElementById('charCount');
    if (messageField && charCount) {
        messageField.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
    }
    
    // Karakter sayacı (contact.php için)
    const messageField2 = document.getElementById('message');
    const charCount2 = document.getElementById('charCount2');
    if (messageField2 && charCount2) {
        messageField2.addEventListener('input', function() {
            charCount2.textContent = this.value.length;
        });
    }
    
    // Contact form normal submission (not AJAX - uses PHP POST)
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        // Normal form submission - PHP handles it
        // No need for AJAX here
    }
});

// Appointment Form Submission (Database)
async function submitAppointment(e) {
    // Prevent default - SUPER IMPORTANT!
    if (e && e.preventDefault) {
        e.preventDefault();
    }
    if (e && e.stopPropagation) {
        e.stopPropagation();
    }
    
    console.log('submitAppointment called with event:', e);
    
    const form = e ? e.target : document.getElementById('appointmentForm');
    if (!form) {
        console.error('Form bulunamadı!');
        alert('Form bulunamadı. Lütfen sayfayı yenileyin.');
        return false;
    }
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Submit button kontrolü
    if (!submitBtn) {
        console.error('Submit button bulunamadı!');
        alert('Form gönderme butonu bulunamadı. Lütfen sayfayı yenileyin.');
        return;
    }
    
    const originalBtnText = submitBtn.innerHTML;
    
    // Get form inputs FROM CURRENT FORM (fix for multiple forms on same page)
    const firstNameInput = form.querySelector('[name="firstName"]');
    const lastNameInput = form.querySelector('[name="lastName"]');
    const phoneInput = form.querySelector('[name="phone"]');
    const appointmentDateInput = form.querySelector('[name="appointmentDate"]');
    const messageInput = form.querySelector('[name="message"]');
    
    // Null check - eğer form elemanları bulunamazsa hata ver
    if (!firstNameInput || !lastNameInput || !phoneInput || !appointmentDateInput || !messageInput) {
        console.error('Form elemanları bulunamadı!', {
            firstNameInput,
            lastNameInput,
            phoneInput,
            appointmentDateInput,
            messageInput
        });
        showToast('Form yüklenirken bir hata oluştu. Lütfen sayfayı yenileyin.', 'error', 'Form Hatası');
        return;
    }
    
    const firstName = firstNameInput.value.trim();
    const lastName = lastNameInput.value.trim();
    const phone = phoneInput.value.trim();
    const appointmentDate = appointmentDateInput.value;
    const message = messageInput.value.trim() || '';
    
    // Validation: İsim kontrolü
    if (!firstName || firstName.length < 2) {
        showToast('Lütfen geçerli bir ad giriniz! (En az 2 karakter)', 'error', 'Eksik Bilgi');
        firstNameInput.focus();
        firstNameInput.style.borderColor = '#e74c3c';
        setTimeout(() => { firstNameInput.style.borderColor = ''; }, 3000);
        return;
    }
    
    // Validation: Soyisim kontrolü
    if (!lastName || lastName.length < 2) {
        showToast('Lütfen geçerli bir soyad giriniz! (En az 2 karakter)', 'error', 'Eksik Bilgi');
        lastNameInput.focus();
        lastNameInput.style.borderColor = '#e74c3c';
        setTimeout(() => { lastNameInput.style.borderColor = ''; }, 3000);
        return;
    }
    
    // Validation: Telefon kontrolü
    if (!phone) {
        showToast('Lütfen telefon numaranızı giriniz!', 'error', 'Eksik Bilgi');
        phoneInput.focus();
        phoneInput.style.borderColor = '#e74c3c';
        setTimeout(() => { phoneInput.style.borderColor = ''; }, 3000);
        return;
    }
    
    // Telefon formatı kontrolü (esnek - tüm formatlar kabul edilir: 0530, +90 530, 90 530 vb.)
    const phoneDigits = phone.replace(/[^0-9]/g, '');
    if (phoneDigits.length < 10) {
        showToast('Telefon numarası en az 10 haneli olmalıdır! (Örn: 05301234567, +90 530 123 45 67)', 'error', 'Geçersiz Telefon');
        phoneInput.focus();
        phoneInput.style.borderColor = '#e74c3c';
        setTimeout(() => { phoneInput.style.borderColor = ''; }, 3000);
        return;
    }
    
    // Validation: Randevu tarihi kontrolü
    if (!appointmentDate) {
        showToast('Lütfen randevu tarihi seçiniz!', 'error', 'Eksik Bilgi');
        appointmentDateInput.focus();
        appointmentDateInput.style.borderColor = '#e74c3c';
        setTimeout(() => { appointmentDateInput.style.borderColor = ''; }, 3000);
        return;
    }
    
    // Validation: Mesaj karakter kontrolü (200 karakter)
    if (message.length > 200) {
        showToast(`Mesajınız çok uzun! Maksimum 200 karakter olmalıdır. (Şu an: ${message.length} karakter)`, 'error', 'Mesaj Çok Uzun');
        messageInput.focus();
        messageInput.style.borderColor = '#e74c3c';
        setTimeout(() => { messageInput.style.borderColor = ''; }, 3000);
        return;
    }
    
    const fullName = `${firstName} ${lastName}`.trim();
    
    // Disable button
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Gönderiliyor...';
    
    // Prepare form data
    const formData = new FormData();
    formData.append('name', fullName);
    formData.append('phone', phoneDigits);
    formData.append('email', '');
    formData.append('appointment_date', appointmentDate);
    formData.append('message', message);
    
    try {
        // Debug: Log form data DETAILED
        console.log('=== APPOINTMENT SUBMISSION DEBUG ===');
        console.log('Full Name:', fullName);
        console.log('Phone:', phoneDigits);
        console.log('Appointment Date:', appointmentDate);
        console.log('Message:', message);
        console.log('FormData entries:', Array.from(formData.entries()));
        console.log('Fetch URL:', '/process_appointment.php');
        console.log('Fetch Method: POST');
        
        const response = await fetch('/process_appointment.php', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams(formData)
        });
        
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        // Response text'i önce al
        const responseText = await response.text();
        console.log('Response text:', responseText);
        
        // JSON parse et
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (parseError) {
            console.error('JSON parse error:', parseError);
            console.error('Received text:', responseText);
            showToast('Sunucu yanıtı beklenmedik formatta. Lütfen sayfayı yenileyip tekrar deneyin.', 'error', 'Format Hatası');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
            return;
        }
        
        console.log('Parsed result:', result);
        
        // Debug bilgilerini göster
        if (result.debug) {
            console.error('SERVER DEBUG INFO:', result.debug);
            console.error('Received Method:', result.debug.method);
            console.error('Expected Method:', result.debug.expected);
        }
        
        if (result.success) {
            showToast(result.message, 'success', 'Randevu Talebi Gönderildi!');
            form.reset();
            
            // Scroll to top smoothly
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            showToast(result.message, 'error', 'İşlem Başarısız');
        }
    } catch (error) {
        console.error('Fetch Error:', error);
        console.error('Error details:', {
            message: error.message,
            stack: error.stack
        });
        showToast('Bir hata oluştu. Lütfen internet bağlantınızı kontrol edip tekrar deneyin.', 'error', 'Bağlantı Hatası');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    }
    
    return false; // Prevent any default form submission
}

