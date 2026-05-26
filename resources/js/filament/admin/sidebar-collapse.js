(() => {
  const GROUPS = [
    "User Management",
    "Room Management",
    "Booking & Reservation",
    "Approval Workflow",
    "Meal Order",
    "Payment & Billing",
    "Feedback",
    "Settings",
  ];
  const SIDEBAR_COLLAPSED_KEY = "adminSidebarCollapsed";
  const SIDEBAR_COLLAPSED_CLASS = "bhms-admin-sidebar-collapsed";
  const desktopQuery = window.matchMedia("(min-width: 1024px)");
  let observer = null;

  const getStoredSidebarCollapsed = () => {
    try {
      return localStorage.getItem(SIDEBAR_COLLAPSED_KEY) === "1";
    } catch {
      return false;
    }
  };

  const setStoredSidebarCollapsed = (value) => {
    try {
      localStorage.setItem(SIDEBAR_COLLAPSED_KEY, value ? "1" : "0");
    } catch {
      // ignore
    }
  };

  const setSidebarCollapsed = (value) => {
    const collapsed = value && desktopQuery.matches;

    document.documentElement.classList.toggle(SIDEBAR_COLLAPSED_CLASS, collapsed);
    document.body?.classList.toggle(SIDEBAR_COLLAPSED_CLASS, collapsed);

    const button = document.querySelector("[data-admin-sidebar-toggle]");

    if (button) {
      button.setAttribute("aria-expanded", String(!collapsed));
      button.setAttribute("title", collapsed ? "Expand sidebar" : "Collapse sidebar");
      button.setAttribute("aria-label", collapsed ? "Expand sidebar" : "Collapse sidebar");
    }
  };

  const normalizePath = (url) => {
    try {
      const parsed = new URL(url, window.location.origin);

      return parsed.pathname.replace(/\/+$/, "") || "/";
    } catch {
      return "";
    }
  };

  const getCurrentPath = () => normalizePath(window.location.href);

  const ensureCollapsedByDefault = () => {
    try {
      const existing = localStorage.getItem("collapsedGroups");
      if (!existing) {
        localStorage.setItem("collapsedGroups", JSON.stringify(GROUPS));
      }
    } catch {
      // ignore
    }
  };

  const readCollapsedGroups = () => {
    try {
      return JSON.parse(localStorage.getItem("collapsedGroups")) || [];
    } catch {
      return [];
    }
  };

  const writeCollapsedGroups = (groups) => {
    try {
      localStorage.setItem("collapsedGroups", JSON.stringify(groups));
    } catch {
      // ignore
    }
  };

  const getActiveItem = () => {
    const currentPath = getCurrentPath();
    const activeByClass =
      document.querySelector(".fi-main-sidebar .fi-sidebar-item.fi-active") ??
      document.querySelector(".fi-main-sidebar .fi-sidebar-item a[aria-current='page']");

    if (activeByClass) {
      return activeByClass;
    }

    return Array.from(document.querySelectorAll(".fi-main-sidebar .fi-sidebar-item a[href]")).find((link) => {
      const linkPath = normalizePath(link.getAttribute("href"));

      return linkPath === currentPath;
    });
  };

  const getActiveGroup = () => {
    const activeItem = getActiveItem();
    const activeGroup =
      activeItem?.closest(".fi-sidebar-group") ??
      document.querySelector(".fi-main-sidebar .fi-sidebar-group.fi-active");

    if (activeGroup) {
      activeGroup.classList.add("fi-active");
    }

    return activeGroup;
  };

  const openActiveGroup = () => {
    const activeGroup = getActiveGroup();

    if (!activeGroup) return;

    const label = activeGroup.getAttribute("data-group-label");

    if (label) {
      writeCollapsedGroups(readCollapsedGroups().filter((group) => group !== label));
    }

    activeGroup.classList.remove("fi-collapsed");

    const items = activeGroup.querySelector(".fi-sidebar-group-items");
    if (items) {
      items.style.display = "";
      items.removeAttribute("hidden");
    }
  };

  const scrollActiveItemIntoView = () => {
    requestAnimationFrame(() => {
      openActiveGroup();

      const activeItem = getActiveItem() ?? getActiveGroup();
      const scroller = document.querySelector(".fi-main-sidebar .fi-sidebar-nav");

      if (!activeItem) return;

      if (!scroller || scroller.scrollHeight <= scroller.clientHeight) {
        activeItem.scrollIntoView({ block: "nearest", behavior: "smooth" });

        return;
      }

      const itemRect = activeItem.getBoundingClientRect();
      const scrollerRect = scroller.getBoundingClientRect();
      const itemTop = itemRect.top - scrollerRect.top + scroller.scrollTop;
      const itemBottom = itemTop + itemRect.height;
      const visibleTop = scroller.scrollTop;
      const visibleBottom = visibleTop + scroller.clientHeight;
      const padding = 16;

      if (itemTop < visibleTop + padding) {
        scroller.scrollTo({ top: Math.max(itemTop - padding, 0), behavior: "smooth" });
      } else if (itemBottom > visibleBottom - padding) {
        scroller.scrollTo({
          top: itemBottom - scroller.clientHeight + padding,
          behavior: "smooth",
        });
      }
    });
  };

  const createToggleIcon = () => `
    <svg class="bhms-sidebar-toggle-icon bhms-sidebar-toggle-icon-collapse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
    <svg class="bhms-sidebar-toggle-icon bhms-sidebar-toggle-icon-expand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
  `;

  const bindToggleButton = (button) => {
    if (!button || button.dataset.adminSidebarToggleBound === "true") return;

    button.dataset.adminSidebarToggleBound = "true";
    button.addEventListener("click", () => {
      const collapsed = !document.documentElement.classList.contains(SIDEBAR_COLLAPSED_CLASS);
      setStoredSidebarCollapsed(collapsed);
      setSidebarCollapsed(collapsed);
      scrollActiveItemIntoView();
    });
  };

  const ensureToggleButton = () => {
    const sidebar = document.querySelector(".fi-main-sidebar");
    const existingButton = sidebar?.querySelector("[data-admin-sidebar-toggle]");

    if (existingButton) {
      bindToggleButton(existingButton);

      return;
    }

    if (!sidebar) return;

    const button = document.createElement("button");
    button.type = "button";
    button.className = "bhms-sidebar-toggle bhms-sidebar-toggle--floating";
    button.dataset.adminSidebarToggle = "true";
    button.innerHTML = createToggleIcon();
    bindToggleButton(button);

    sidebar.prepend(button);
  };

  const addGroupTooltips = () => {
    document.querySelectorAll(".fi-main-sidebar .fi-sidebar-group[data-group-label]").forEach((group) => {
      const label = group.getAttribute("data-group-label");
      const button = group.querySelector(".fi-sidebar-group-btn");

      if (label && button) {
        button.setAttribute("title", label);
      }
    });
  };

  const init = () => {
    ensureCollapsedByDefault();
    openActiveGroup();
    ensureToggleButton();
    addGroupTooltips();
    setSidebarCollapsed(getStoredSidebarCollapsed());
    scrollActiveItemIntoView();
  };

  const scheduleInit = () => {
    requestAnimationFrame(() => {
      init();

      setTimeout(init, 75);
    });
  };

  const observeSidebar = () => {
    const sidebar = document.querySelector(".fi-main-sidebar");

    if (!sidebar || observer) return;

    observer = new MutationObserver(() => {
      ensureToggleButton();
      addGroupTooltips();
      setSidebarCollapsed(getStoredSidebarCollapsed());
    });

    observer.observe(sidebar, {
      childList: true,
      subtree: true,
    });
  };

  scheduleInit();
  observeSidebar();

  const isSidebarOpen = () => {
    const main = document.querySelector(".fi-main-ctn");
    return !!main?.classList.contains("fi-main-ctn-sidebar-open");
  };

  const openSidebar = () => {
    const openBtn =
      document.querySelector(".fi-topbar-open-sidebar-btn") ??
      document.querySelector(".fi-layout-sidebar-toggle-btn-ctn .fi-layout-sidebar-toggle-btn") ??
      document.querySelector(".fi-layout-sidebar-toggle-btn");

    openBtn?.dispatchEvent(new MouseEvent("click", { bubbles: true, cancelable: true }));
  };

  document.addEventListener(
    "click",
    (e) => {
      const target = e.target instanceof Element ? e.target : null;
      if (!target) return;

      const clickedSidebarToggle =
        target.closest(".fi-sidebar-group-btn") ||
        target.closest(".fi-sidebar-item > a") ||
        target.closest(".fi-sidebar-item-btn");

      if (!clickedSidebarToggle) return;
      if (isSidebarOpen()) return;

      openSidebar();
    },
    true,
  );

  desktopQuery.addEventListener?.("change", () => setSidebarCollapsed(getStoredSidebarCollapsed()));
  document.addEventListener("DOMContentLoaded", scheduleInit);
  document.addEventListener("livewire:navigated", () => {
    scheduleInit();
    observeSidebar();
  });
})();
