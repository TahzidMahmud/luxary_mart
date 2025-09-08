import React from "react";

type Props = {
  children: React.ReactNode;
  isOpen: boolean;
  setIsOpen: (v: boolean) => void;
  // optional sizes you can tweak
  drawerWidth?: number; // px
  handleWidth?: number; // px
};

export default function DrawerWithHandle({
  children,
  isOpen,
  setIsOpen,
  drawerWidth = 360,
  handleWidth = 56,
}: Props) {
  // closedTranslate moves the drawer right so that `handleWidth` remains visible
  const closedTranslate = `calc(100% - ${handleWidth}px)`; // translateX value

  return (
    <>
      {/* overlay when open */}
      {isOpen && (
        <div
          className="fixed inset-0 bg-black/40 z-40"
          onClick={() => setIsOpen(false)}
        />
      )}

      {/* wrapper sized to the drawer width (keeps layout predictable) */}
      <div
        className="fixed inset-y-0 right-0 z-50 pointer-events-none"
        style={{ width: `${drawerWidth}px` }}
      >
        {/* sliding panel:
            - translateX(0) when open
            - translateX(calc(100% - handleWidth)) when closed so only `handleWidth` of the panel remains visible
            - pointer-events enabled only for the panel itself so clicks on overlay work normally
        */}
        <div
          className="relative h-full pointer-events-auto transition-transform duration-300 ease-out"
          style={{
            width: "100%",
            transform: isOpen ? "translateX(0)" : `translateX(${closedTranslate})`,
          }}
        >
          {/* Panel content: use your app's background to remove white border */}
          <div className="h-full overflow-y-auto shadow-lg" style={{ background: "var(--theme-background, #0b1220)" }}>
            {children}
          </div>

          {/* Handle attached to the **left edge** of the drawer, vertically centered */}
          <button
            onClick={() => setIsOpen(!isOpen)}
            aria-label={isOpen ? "Close cart" : "Open cart"}
            className="absolute top-1/2 left-0 -translate-y-1/2 rounded-r-lg shadow-lg flex items-center justify-center select-none"
            style={{
              width: `${handleWidth}px`,
              height: `${handleWidth}px`,
              // visually style the handle
              background: "#2b6cb0", // change to your theme color
              color: "white",
              border: "none",
              // keep it above the panel contents
              zIndex: 60,
              // move the handle half-outside visually (optional):
              transform: isOpen ? "translateY(-50%)" : "translateY(-50%)",
            }}
          >
            {/* simple icon/text for handle; change to icon svg if you like */}
            <span style={{ fontWeight: 700 }}>{isOpen ? "⟩" : "⟨"}</span>
          </button>
        </div>
      </div>
    </>
  );
}
