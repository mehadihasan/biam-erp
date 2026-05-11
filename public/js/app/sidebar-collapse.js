(() => {
  const GROUPS = ["User Management", "Room Management", "Booking & Reservation"];

  const ensureCollapsedByDefault = () => {
    try {
      // Only set defaults once — don't fight the user's toggle choice.
      const existing = localStorage.getItem("collapsedGroups");
      if (!existing) {
        localStorage.setItem("collapsedGroups", JSON.stringify(GROUPS));
      }
    } catch {
      // ignore
    }
  };

  // Initial page load
  ensureCollapsedByDefault();

  const isSidebarOpen = () => {
    // This class is toggled by Filament's Alpine store binding.
    const main = document.querySelector(".fi-main-ctn");
    return !!main?.classList.contains("fi-main-ctn-sidebar-open");
  };

  const openSidebar = () => {
    // Desktop topbar button (top navigation) OR layout toggle button (sidebar layout).
    const openBtn =
      document.querySelector(".fi-topbar-open-sidebar-btn") ??
      document.querySelector(".fi-layout-sidebar-toggle-btn-ctn .fi-layout-sidebar-toggle-btn") ??
      document.querySelector(".fi-layout-sidebar-toggle-btn");

    openBtn?.dispatchEvent(new MouseEvent("click", { bubbles: true, cancelable: true }));
  };

  // If the sidebar is collapsed/closed, clicking a group/sub-item should open it first.
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

  // Filament / Livewire SPA-like navigation
  document.addEventListener("livewire:navigated", ensureCollapsedByDefault);
})();

