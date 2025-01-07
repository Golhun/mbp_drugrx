// ./js/bubbleInfo.js

(function() {
    // Caches composition info to avoid multiple requests for the same drug
    const drugInfoCache = {};
  
    /**
     * Attaches an info icon + pop-up to each bubble in `containerSelector`
     * Only for screens >= 768px (desktop). On mobile, we do nothing.
     */
    window.attachBubbleInfo = function(containerSelector) {
      const container = document.querySelector(containerSelector);
      if (!container) return;
  
      // For each bubble with [data-drug-bubble]
      const bubbles = container.querySelectorAll("[data-drug-bubble]");
      bubbles.forEach((bubble) => {
        const drugName = bubble.getAttribute("data-drug-name");
        if (!drugName) return;
  
        // If we’ve already appended an icon or done a fetch, skip
        if (bubble.querySelector(".bubble-info-icon")) return;
  
        // Check if we have a cached result
        if (drugInfoCache[drugName]) {
          // If found in cache and found=true => build the icon/pop-up
          if (drugInfoCache[drugName].found) {
            addIconAndPopUp(bubble, drugName, drugInfoCache[drugName]);
          }
          // If found=false => do nothing
          return;
        }
  
        // Otherwise, fetch from server
        fetch("../server.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ type: "druginfo", drugName })
        })
        .then((res) => res.json())
        .then((data) => {
          // Cache the result to avoid repeated requests
          if (data.error) {
            console.warn("drugInfo error:", data.error);
            // also store in cache so we won't fetch again
            drugInfoCache[drugName] = { found: false };
            return;
          }
  
          // If data.found => store it
          if (data.found) {
            drugInfoCache[drugName] = {
              found: true,
              composition: data.composition,
              uses: data.uses,
              side_effects: data.side_effects
            };
            addIconAndPopUp(bubble, drugName, drugInfoCache[drugName]);
          } else {
            drugInfoCache[drugName] = { found: false };
          }
        })
        .catch((err) => {
          console.error("Drug info fetch error:", err);
          // Also store a negative response to avoid repeated attempts
          drugInfoCache[drugName] = { found: false };
        });
      });
    };
  
    // Helper to create an icon + pop-up
    function addIconAndPopUp(bubble, drugName, info) {
      // If not found, do nothing
      if (!info.found) return;
  
      // Only do hover icon on desktop
      if (window.innerWidth < 768) return;
  
      // Create icon
      const iconSpan = document.createElement("span");
      iconSpan.classList.add(
        "bubble-info-icon",
        "material-icons",
        "text-blue-500",
        "ml-2",
        "cursor-pointer"
      );
      iconSpan.textContent = "info"; // "info" icon
  
      // Create pop-up
      const popUp = document.createElement("div");
      popUp.classList.add(
        "hidden",
        "absolute",
        "bg-white",
        "text-gray-800",
        "p-3",
        "rounded",
        "shadow-lg",
        "z-50",
        "max-w-sm"
      );
      popUp.innerHTML = `
        <h4 class="font-bold mb-2 text-pink-600">${drugName}</h4>
        <p class="text-sm mb-1"><strong>Uses:</strong> ${info.uses || "N/A"}</p>
        <p class="text-sm"><strong>Side Effects:</strong> ${info.side_effects || "N/A"}</p>
      `;
  
      // Insert pop-up into bubble
      bubble.appendChild(popUp);
  
      // Show pop-up on icon hover
      iconSpan.addEventListener("mouseenter", () => {
        popUp.classList.remove("hidden");
      });
      iconSpan.addEventListener("mouseleave", () => {
        popUp.classList.add("hidden");
      });
  
      // Position popUp near the icon
      bubble.style.position = "relative";
      popUp.style.top = "30px";
      popUp.style.right = "0px";
  
      // Append iconSpan to bubble
      bubble.appendChild(iconSpan);
    }
  })();
  