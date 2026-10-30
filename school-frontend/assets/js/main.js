/**
 * ============================================================================
 * SISTEM SEKOLAH — MAIN FRONTEND INTERACTIONS (Vanilla ES6+ JavaScript)
 * Compatible with static HTML preview & Laravel 13 + Livewire 4 Blade integration
 * ============================================================================
 */

(function () {
  'use strict';

  /* --------------------------------------------------------------------------
   * 1. STICKY HEADER & SCROLL TO TOP
   * -------------------------------------------------------------------------- */
  function initScrollEffects() {
    const header = document.getElementById('site-header');
    const scrollTopBtn = document.getElementById('scroll-to-top');

    function handleScroll() {
      const scrollY = window.scrollY || window.pageYOffset;
      if (header) {
        if (scrollY > 16) {
          header.classList.add('is-scrolled');
        } else {
          header.classList.remove('is-scrolled');
        }
      }
      if (scrollTopBtn) {
        if (scrollY > 360) {
          scrollTopBtn.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-4');
          scrollTopBtn.classList.add('opacity-100', 'translate-y-0');
        } else {
          scrollTopBtn.classList.add('opacity-0', 'pointer-events-none', 'translate-y-4');
          scrollTopBtn.classList.remove('opacity-100', 'translate-y-0');
        }
      }
    }

    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();

    if (scrollTopBtn) {
      scrollTopBtn.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    }
  }

  /* --------------------------------------------------------------------------
   * 2. DESKTOP DROPDOWNS & KEYBOARD NAVIGATION
   * -------------------------------------------------------------------------- */
  function initDesktopDropdowns() {
    const dropdowns = document.querySelectorAll('.nav-dropdown');
    if (!dropdowns.length) return;

    function closeAllDropdowns(except) {
      dropdowns.forEach(function (dd) {
        if (dd !== except) {
          dd.classList.remove('open');
          const btn = dd.querySelector('[data-dropdown-toggle]');
          if (btn) btn.setAttribute('aria-expanded', 'false');
        }
      });
    }

    dropdowns.forEach(function (dd) {
      const btn = dd.querySelector('[data-dropdown-toggle]');
      if (!btn) return;

      btn.addEventListener('click', function (e) {
        e.stopPropagation();
        const isOpen = dd.classList.contains('open');
        closeAllDropdowns(dd);
        dd.classList.toggle('open', !isOpen);
        btn.setAttribute('aria-expanded', String(!isOpen));
      });

      btn.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
          dd.classList.remove('open');
          btn.setAttribute('aria-expanded', 'false');
          btn.focus();
        }
      });
    });

    document.addEventListener('click', function (e) {
      if (!e.target.closest('.nav-dropdown')) {
        closeAllDropdowns(null);
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        closeAllDropdowns(null);
      }
    });
  }

  /* --------------------------------------------------------------------------
   * 3. MOBILE NAVIGATION DRAWER & ACCORDIONS
   * -------------------------------------------------------------------------- */
  function initMobileNavigation() {
    const openBtn = document.getElementById('mobile-menu-open');
    const closeBtn = document.getElementById('mobile-menu-close');
    const backdrop = document.getElementById('mobile-menu-backdrop');
    const drawer = document.getElementById('mobile-menu-drawer');

    if (!openBtn || !drawer) return;

    function openMobileMenu() {
      drawer.classList.remove('translate-x-full');
      drawer.classList.add('translate-x-0');
      if (backdrop) {
        backdrop.classList.remove('opacity-0', 'pointer-events-none');
        backdrop.classList.add('opacity-100');
      }
      openBtn.setAttribute('aria-expanded', 'true');
      document.body.classList.add('overflow-hidden-lock');
    }

    function closeMobileMenu() {
      drawer.classList.add('translate-x-full');
      drawer.classList.remove('translate-x-0');
      if (backdrop) {
        backdrop.classList.add('opacity-0', 'pointer-events-none');
        backdrop.classList.remove('opacity-100');
      }
      openBtn.setAttribute('aria-expanded', 'false');
      document.body.classList.remove('overflow-hidden-lock');
    }

    openBtn.addEventListener('click', openMobileMenu);
    if (closeBtn) closeBtn.addEventListener('click', closeMobileMenu);
    if (backdrop) backdrop.addEventListener('click', closeMobileMenu);

    // Close mobile menu when clicking a link inside it
    drawer.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', closeMobileMenu);
    });

    // Mobile Accordion Submenus
    const accordions = drawer.querySelectorAll('[data-mobile-accordion]');
    accordions.forEach(function (btn) {
      btn.addEventListener('click', function () {
        const targetId = btn.getAttribute('data-mobile-accordion');
        const panel = document.getElementById(targetId);
        const icon = btn.querySelector('[data-accordion-icon]');
        if (!panel) return;

        const isHidden = panel.classList.contains('hidden');
        panel.classList.toggle('hidden', !isHidden);
        btn.setAttribute('aria-expanded', String(isHidden));
        if (icon) {
          icon.classList.toggle('rotate-180', isHidden);
        }
      });
    });
  }

  /* --------------------------------------------------------------------------
   * 4. HERO SLIDER (HOMEPAGE)
   * -------------------------------------------------------------------------- */
  let heroIntervalId = null;

  function initHeroSlider() {
    const slider = document.querySelector('[data-hero-slider]');
    if (!slider) return;

    const slides = slider.querySelectorAll('.hero-slide');
    const dots = slider.querySelectorAll('[data-hero-dot]');
    const prevBtn = slider.querySelector('[data-hero-prev]');
    const nextBtn = slider.querySelector('[data-hero-next]');

    if (!slides.length) return;
    let currentIndex = 0;

    function showSlide(index) {
      currentIndex = (index + slides.length) % slides.length;
      slides.forEach(function (slide, idx) {
        const active = idx === currentIndex;
        slide.classList.toggle('active', active);
        slide.setAttribute('aria-hidden', String(!active));
      });
      dots.forEach(function (dot, idx) {
        const active = idx === currentIndex;
        if (active) {
          dot.classList.add('w-8', 'bg-[#F59E0B]');
          dot.classList.remove('w-2.5', 'bg-white/50');
          dot.setAttribute('aria-current', 'true');
        } else {
          dot.classList.remove('w-8', 'bg-[#F59E0B]');
          dot.classList.add('w-2.5', 'bg-white/50');
          dot.removeAttribute('aria-current');
        }
      });
    }

    function startAutoPlay() {
      if (heroIntervalId) clearInterval(heroIntervalId);
      heroIntervalId = setInterval(function () {
        showSlide(currentIndex + 1);
      }, 6500);
    }

    if (prevBtn) {
      prevBtn.addEventListener('click', function () {
        showSlide(currentIndex - 1);
        startAutoPlay();
      });
    }

    if (nextBtn) {
      nextBtn.addEventListener('click', function () {
        showSlide(currentIndex + 1);
        startAutoPlay();
      });
    }

    dots.forEach(function (dot, idx) {
      dot.addEventListener('click', function () {
        showSlide(idx);
        startAutoPlay();
      });
    });

    slider.addEventListener('mouseenter', function () {
      if (heroIntervalId) clearInterval(heroIntervalId);
    });
    slider.addEventListener('mouseleave', startAutoPlay);

    showSlide(0);
    startAutoPlay();
  }

  /* --------------------------------------------------------------------------
   * 5. REUSABLE TABS (AKADEMIK, KEPEGAWAIAN, ETC.)
   * -------------------------------------------------------------------------- */
  function initTabs() {
    const tabContainers = document.querySelectorAll('[data-tabs-container]');
    tabContainers.forEach(function (container) {
      const buttons = container.querySelectorAll('[data-tab-target]');
      const panels = container.querySelectorAll('[data-tab-panel]');

      buttons.forEach(function (btn) {
        btn.addEventListener('click', function () {
          const target = btn.getAttribute('data-tab-target');

          buttons.forEach(function (b) {
            const isActive = b === btn;
            b.setAttribute('aria-selected', String(isActive));
            if (isActive) {
              b.classList.add('bg-[#2563EB]', 'text-white', 'shadow-sm', 'border-[#2563EB]');
              b.classList.remove('bg-white', 'text-[#64748B]', 'hover:text-[#0F172A]', 'border-slate-200');
            } else {
              b.classList.remove('bg-[#2563EB]', 'text-white', 'shadow-sm', 'border-[#2563EB]');
              b.classList.add('bg-white', 'text-[#64748B]', 'hover:text-[#0F172A]', 'border-slate-200');
            }
          });

          panels.forEach(function (panel) {
            const match = panel.getAttribute('data-tab-panel') === target;
            panel.classList.toggle('hidden', !match);
          });
        });
      });
    });
  }

  /* --------------------------------------------------------------------------
   * 6. ACCORDION FAQ (PPDB, KONTAK, ETC.)
   * -------------------------------------------------------------------------- */
  function initAccordions() {
    const faqButtons = document.querySelectorAll('[data-faq-toggle]');
    faqButtons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        const targetId = btn.getAttribute('data-faq-toggle');
        const content = document.getElementById(targetId);
        const icon = btn.querySelector('[data-faq-icon]');
        if (!content) return;

        const isHidden = content.classList.contains('hidden');
        content.classList.toggle('hidden', !isHidden);
        btn.setAttribute('aria-expanded', String(isHidden));
        if (icon) {
          icon.classList.toggle('rotate-180', isHidden);
        }
      });
    });
  }

  /* --------------------------------------------------------------------------
   * 7. GALLERY LIGHTBOX MODAL
   * -------------------------------------------------------------------------- */
  function initLightbox() {
    const modal = document.getElementById('lightbox-modal');
    if (!modal) return;

    const modalImg = document.getElementById('lightbox-image');
    const modalTitle = document.getElementById('lightbox-title');
    const modalCategory = document.getElementById('lightbox-category');
    const modalDesc = document.getElementById('lightbox-desc');
    const closeBtn = document.getElementById('lightbox-close');
    const backdrop = document.getElementById('lightbox-backdrop');

    function openLightbox(trigger) {
      const src = trigger.getAttribute('data-lightbox-src');
      const title = trigger.getAttribute('data-lightbox-title') || 'Dokumentasi Sekolah';
      const category = trigger.getAttribute('data-lightbox-category') || 'Galeri Kegiatan';
      const desc = trigger.getAttribute('data-lightbox-desc') || '';

      if (modalImg && src) {
        modalImg.src = src;
        modalImg.alt = title;
      }
      if (modalTitle) modalTitle.textContent = title;
      if (modalCategory) modalCategory.textContent = category;
      if (modalDesc) modalDesc.textContent = desc;

      modal.classList.remove('hidden');
      modal.classList.add('flex');
      modal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('overflow-hidden-lock');
    }

    function closeLightbox() {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
      modal.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('overflow-hidden-lock');
    }

    document.querySelectorAll('[data-lightbox-src]').forEach(function (item) {
      item.addEventListener('click', function (e) {
        e.preventDefault();
        openLightbox(item);
      });
    });

    if (closeBtn) closeBtn.addEventListener('click', closeLightbox);
    if (backdrop) backdrop.addEventListener('click', closeLightbox);

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
        closeLightbox();
      }
    });
  }

  /* --------------------------------------------------------------------------
   * 8. LIVE FILTER & SEARCH (KEPEGAWAIAN, BLOG, GALERI, PRESTASI, PENGUMUMAN, AGENDA)
   * -------------------------------------------------------------------------- */
  function initFilterAndSearch() {
    const filterSystems = document.querySelectorAll('[data-filter-system]');

    filterSystems.forEach(function (system) {
      const searchInput = system.querySelector('[data-filter-search]');
      const categorySelect = system.querySelector('[data-filter-select]');
      const yearSelect = system.querySelector('[data-filter-year]');
      const pillButtons = system.querySelectorAll('[data-filter-pill]');
      const items = system.querySelectorAll('[data-filter-item]');
      const emptyState = system.querySelector('[data-filter-empty]');
      const countBadge = system.querySelector('[data-filter-count]');
      const resetBtn = system.querySelector('[data-filter-reset]');

      let activePill = 'semua';

      function applyFilters() {
        const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
        const selectedCategory = categorySelect ? categorySelect.value.toLowerCase() : activePill;
        const selectedYear = yearSelect ? yearSelect.value.toLowerCase() : 'semua';

        let visibleCount = 0;

        items.forEach(function (item) {
          const itemText = (item.getAttribute('data-title') || item.textContent || '').toLowerCase();
          const itemCategory = (item.getAttribute('data-category') || '').toLowerCase();
          const itemYear = (item.getAttribute('data-year') || '').toLowerCase();

          const matchesSearch = !query || itemText.includes(query);
          const matchesCategory =
            selectedCategory === 'semua' ||
            !selectedCategory ||
            itemCategory === selectedCategory ||
            itemCategory.includes(selectedCategory);
          const matchesYear =
            selectedYear === 'semua' || !selectedYear || itemYear === selectedYear;

          const isVisible = matchesSearch && matchesCategory && matchesYear;
          item.classList.toggle('hidden', !isVisible);
          if (isVisible) visibleCount++;
        });

        if (emptyState) {
          emptyState.classList.toggle('hidden', visibleCount > 0);
        }
        if (countBadge) {
          countBadge.textContent = String(visibleCount);
        }
      }

      if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
      }
      if (categorySelect) {
        categorySelect.addEventListener('change', applyFilters);
      }
      if (yearSelect) {
        yearSelect.addEventListener('change', applyFilters);
      }

      pillButtons.forEach(function (pill) {
        pill.addEventListener('click', function () {
          activePill = (pill.getAttribute('data-filter-pill') || 'semua').toLowerCase();
          pillButtons.forEach(function (p) {
            const isCurrent = p === pill;
            if (isCurrent) {
              p.classList.add('bg-[#2563EB]', 'text-white', 'border-[#2563EB]');
              p.classList.remove('bg-white', 'text-[#64748B]', 'border-slate-200');
            } else {
              p.classList.remove('bg-[#2563EB]', 'text-white', 'border-[#2563EB]');
              p.classList.add('bg-white', 'text-[#64748B]', 'border-slate-200');
            }
          });
          if (categorySelect) categorySelect.value = activePill;
          applyFilters();
        });
      });

      if (resetBtn) {
        resetBtn.addEventListener('click', function () {
          if (searchInput) searchInput.value = '';
          if (categorySelect) categorySelect.value = 'semua';
          if (yearSelect) yearSelect.value = 'semua';
          activePill = 'semua';
          pillButtons.forEach(function (p, idx) {
            const isFirst = idx === 0;
            p.classList.toggle('bg-[#2563EB]', isFirst);
            p.classList.toggle('text-white', isFirst);
            p.classList.toggle('border-[#2563EB]', isFirst);
            p.classList.toggle('bg-white', !isFirst);
            p.classList.toggle('text-[#64748B]', !isFirst);
            p.classList.toggle('border-slate-200', !isFirst);
          });
          applyFilters();
        });
      }
    });
  }

  /* --------------------------------------------------------------------------
   * 9. FORM VALIDATION & DEMO STATES (PPDB, KONTAK, LOGIN)
   * -------------------------------------------------------------------------- */
  function initForms() {
    // Password visibility toggle on Login page
    const togglePassBtns = document.querySelectorAll('[data-toggle-password]');
    togglePassBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        const targetId = btn.getAttribute('data-toggle-password');
        const input = document.getElementById(targetId);
        if (!input) return;
        const isPass = input.type === 'password';
        input.type = isPass ? 'text' : 'password';
        btn.setAttribute('aria-label', isPass ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        const labelSpan = btn.querySelector('[data-pass-label]');
        if (labelSpan) labelSpan.textContent = isPass ? 'Sembunyikan' : 'Tampilkan';
      });
    });

    // PPDB Registration Demo Form
    const ppdbForm = document.getElementById('ppdb-form');
    if (ppdbForm) {
      const submitBtn = document.getElementById('ppdb-submit-btn');
      const fillDemoBtn = document.getElementById('ppdb-fill-demo');
      const resetDemoBtn = document.getElementById('ppdb-reset-btn');
      const alertSuccess = document.getElementById('ppdb-alert-success');
      const alertError = document.getElementById('ppdb-alert-error');

      function validateField(input) {
        const wrapper = input.closest('[data-field-group]');
        const errEl = wrapper ? wrapper.querySelector('[data-field-error]') : null;
        let errorMsg = '';

        const val = input.value.trim();
        if (input.hasAttribute('required') && !val) {
          errorMsg = 'Kolom ini wajib diisi.';
        } else if (input.name === 'nisn' && val && !/^\d{10}$/.test(val)) {
          errorMsg = 'NISN harus terdiri dari 10 digit angka.';
        } else if (input.name === 'nik' && val && !/^\d{16}$/.test(val)) {
          errorMsg = 'NIK harus terdiri dari 16 digit angka.';
        } else if (input.type === 'email' && val && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
          errorMsg = 'Format alamat email tidak valid.';
        } else if (input.name === 'no_hp' && val && !/^[0-9+\-\s]{10,15}$/.test(val)) {
          errorMsg = 'Nomor HP harus berisi 10–15 digit angka.';
        }

        if (errorMsg) {
          input.classList.add('border-red-500', 'bg-red-50/30');
          input.classList.remove('border-slate-300', 'border-emerald-500');
          if (errEl) {
            errEl.textContent = errorMsg;
            errEl.classList.remove('hidden');
          }
          return false;
        } else {
          input.classList.remove('border-red-500', 'bg-red-50/30');
          if (val) input.classList.add('border-emerald-500');
          if (errEl) {
            errEl.textContent = '';
            errEl.classList.add('hidden');
          }
          return true;
        }
      }

      ppdbForm.querySelectorAll('input, select, textarea').forEach(function (field) {
        field.addEventListener('blur', function () {
          validateField(field);
        });
        field.addEventListener('input', function () {
          if (field.classList.contains('border-red-500')) {
            validateField(field);
          }
        });
      });

      if (fillDemoBtn) {
        fillDemoBtn.addEventListener('click', function () {
          const demoData = {
            nama_lengkap: 'Siswa Contoh Simulasi',
            nisn: '0098765432',
            nik: '3201012010050001',
            tempat_lahir: 'Kota Contoh',
            tanggal_lahir: '2011-05-14',
            jenis_kelamin: 'Laki-laki',
            sekolah_asal: 'SMP Contoh Harapan 01',
            jenjang: 'SMA',
            jurusan: 'Program Sains & Teknologi (IPA)',
            nama_ortu: 'Wali Contoh Simulasi',
            no_hp: '081200001234',
            email: 'calonsiswa.demo@contoh.sch.id',
            alamat: 'Jl. Pendidikan Raya No. 10, Kec. Akademika, Kota Contoh (Data Simulasi UI)'
          };
          Object.keys(demoData).forEach(function (key) {
            const el = ppdbForm.elements[key];
            if (el) {
              el.value = demoData[key];
              validateField(el);
            }
          });
          if (alertError) alertError.classList.add('hidden');
        });
      }

      if (resetDemoBtn) {
        resetDemoBtn.addEventListener('click', function () {
          ppdbForm.reset();
          ppdbForm.querySelectorAll('input, select, textarea').forEach(function (field) {
            field.classList.remove('border-red-500', 'bg-red-50/30', 'border-emerald-500');
          });
          ppdbForm.querySelectorAll('[data-field-error]').forEach(function (err) {
            err.classList.add('hidden');
          });
          if (alertSuccess) alertSuccess.classList.add('hidden');
          if (alertError) alertError.classList.add('hidden');
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span>Kirim Formulir Pendaftaran Awal (Demo UI)</span>';
          }
        });
      }

      ppdbForm.addEventListener('submit', function (e) {
        e.preventDefault();
        let allValid = true;
        ppdbForm.querySelectorAll('input[required], select[required], textarea[required]').forEach(function (field) {
          if (!validateField(field)) {
            allValid = false;
          }
        });

        if (!allValid) {
          if (alertError) alertError.classList.remove('hidden');
          if (alertSuccess) alertSuccess.classList.add('hidden');
          return;
        }

        if (alertError) alertError.classList.add('hidden');
        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.innerHTML = '<span class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent mr-2"></span><span>Memverifikasi Data Simulasi...</span>';
        }

        setTimeout(function () {
          if (alertSuccess) {
            alertSuccess.classList.remove('hidden');
            alertSuccess.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
          }
          if (submitBtn) {
            submitBtn.innerHTML = '<span>Pendaftaran Simulasi Terkirim (Mode Demo)</span>';
          }
        }, 900);
      });
    }

    // Contact Form Demo
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
      const contactBtn = document.getElementById('contact-submit-btn');
      const contactSuccess = document.getElementById('contact-alert-success');
      const contactError = document.getElementById('contact-alert-error');

      contactForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const name = (contactForm.elements['contact_name']?.value || '').trim();
        const email = (contactForm.elements['contact_email']?.value || '').trim();
        const subject = (contactForm.elements['contact_subject']?.value || '').trim();
        const message = (contactForm.elements['contact_message']?.value || '').trim();

        if (!name || !email || !subject || !message || !email.includes('@')) {
          if (contactError) contactError.classList.remove('hidden');
          if (contactSuccess) contactSuccess.classList.add('hidden');
          return;
        }

        if (contactError) contactError.classList.add('hidden');
        if (contactBtn) {
          contactBtn.disabled = true;
          contactBtn.textContent = 'Mengirim Pesan Simulasi...';
        }

        setTimeout(function () {
          if (contactSuccess) contactSuccess.classList.remove('hidden');
          if (contactBtn) {
            contactBtn.disabled = false;
            contactBtn.textContent = 'Kirim Pesan Sekarang';
          }
          contactForm.reset();
        }, 700);
      });
    }

    // Login Portal Demo Form
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
      const loginBtn = document.getElementById('login-submit-btn');
      const loginSuccess = document.getElementById('login-alert-success');
      const loginError = document.getElementById('login-alert-error');
      const demoCredsBtn = document.getElementById('login-fill-demo');

      if (demoCredsBtn) {
        demoCredsBtn.addEventListener('click', function () {
          const userEl = document.getElementById('login-identity');
          const passEl = document.getElementById('login-password');
          if (userEl) userEl.value = 'guru.demo@namasekolah.sch.id';
          if (passEl) passEl.value = 'PortalSekolah2026!';
          if (loginError) loginError.classList.add('hidden');
        });
      }

      loginForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const userEl = document.getElementById('login-identity');
        const passEl = document.getElementById('login-password');
        const userVal = userEl ? userEl.value.trim() : '';
        const passVal = passEl ? passEl.value.trim() : '';

        if (!userVal || passVal.length < 6) {
          if (loginError) loginError.classList.remove('hidden');
          if (loginSuccess) loginSuccess.classList.add('hidden');
          return;
        }

        if (loginError) loginError.classList.add('hidden');
        if (loginBtn) {
          loginBtn.disabled = true;
          loginBtn.textContent = 'Memeriksa Sesi Portal (Demo)...';
        }

        setTimeout(function () {
          if (loginSuccess) loginSuccess.classList.remove('hidden');
          if (loginBtn) {
            loginBtn.disabled = false;
            loginBtn.textContent = 'Masuk ke Portal Akademik';
          }
        }, 800);
      });
    }
  }

  /* --------------------------------------------------------------------------
   * 10. SHARE BUTTONS & ATTACHMENT PREVIEW MODAL (BLOG DETAIL & PENGUMUMAN)
   * -------------------------------------------------------------------------- */
  function initUtilityActions() {
    // Copy Link / Share Toast
    const copyBtns = document.querySelectorAll('[data-copy-link]');
    const toast = document.getElementById('action-toast');
    const toastText = document.getElementById('action-toast-text');

    function showToast(msg) {
      if (!toast) return;
      if (toastText) toastText.textContent = msg;
      toast.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-4');
      toast.classList.add('opacity-100', 'translate-y-0');
      setTimeout(function () {
        toast.classList.add('opacity-0', 'pointer-events-none', 'translate-y-4');
        toast.classList.remove('opacity-100', 'translate-y-0');
      }, 3000);
    }

    copyBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        const msg = btn.getAttribute('data-copy-link') || 'Tautan halaman berhasil disalin ke papan klip!';
        try {
          if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(window.location.href);
          }
        } catch (err) {
          // Ignore clipboard errors in sandbox
        }
        showToast(msg);
      });
    });

    // Document Attachment Preview Modal (Pengumuman)
    const docModal = document.getElementById('doc-preview-modal');
    if (docModal) {
      const docTitle = document.getElementById('doc-preview-title');
      const docMeta = document.getElementById('doc-preview-meta');
      const docClose = document.getElementById('doc-preview-close');
      const docBackdrop = document.getElementById('doc-preview-backdrop');

      function closeDocModal() {
        docModal.classList.add('hidden');
        docModal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden-lock');
      }

      document.querySelectorAll('[data-doc-preview]').forEach(function (btn) {
        btn.addEventListener('click', function () {
          const title = btn.getAttribute('data-doc-preview') || 'Lampiran Pengumuman Resmi.pdf';
          const meta = btn.getAttribute('data-doc-meta') || 'PDF • 420 KB • Dokumen Resmi Sekolah';
          if (docTitle) docTitle.textContent = title;
          if (docMeta) docMeta.textContent = meta;
          docModal.classList.remove('hidden');
          docModal.classList.add('flex');
          document.body.classList.add('overflow-hidden-lock');
        });
      });

      if (docClose) docClose.addEventListener('click', closeDocModal);
      if (docBackdrop) docBackdrop.addEventListener('click', closeDocModal);
    }
  }

  /* --------------------------------------------------------------------------
   * INITIALIZE ALL MODULES
   * -------------------------------------------------------------------------- */
  function initAll() {
    initScrollEffects();
    initDesktopDropdowns();
    initMobileNavigation();
    initHeroSlider();
    initTabs();
    initAccordions();
    initLightbox();
    initFilterAndSearch();
    initForms();
    initUtilityActions();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }
})();
