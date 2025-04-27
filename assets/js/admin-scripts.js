/**************************************
Selection of the checkboxes
***************************************/
function toggleSelectAll(source) {
  const checkboxes = document.querySelectorAll(".wpdba-bulk-checkbox");
  checkboxes.forEach((checkbox) => {
    checkbox.checked = source.checked;
  });
}
/****************************************
Bulk Delete, enable and disable autoload
****************************************/
document.addEventListener("DOMContentLoaded", function () {
  const enableAutoloadButton = document.getElementById("bulk-enable-autoload");
  const disableAutoloadButton = document.getElementById(
    "bulk-disable-autoload"
  );
  const bulkDeleteButton = document.getElementById("bulk-delete");
  const checkboxes = document.querySelectorAll(".wpdba-bulk-checkbox");
  const ajaxUrl = wpdba_ajax.ajaxurl;

  // Function to get selected IDs
  function getSelectedIds() {
    return Array.from(checkboxes)
      .filter((cb) => cb.checked)
      .map((cb) => cb.value);
  }

  // Handle bulk enable autoload
  if (enableAutoloadButton) {
    enableAutoloadButton.addEventListener("click", function () {
      handleAutoloadChange("enable");
    });
  }

  // Handle bulk disable autoload
  if (disableAutoloadButton) {
    disableAutoloadButton.addEventListener("click", function () {
      handleAutoloadChange("disable");
    });
  }

  // Handle bulk delete
  if (bulkDeleteButton) {
    bulkDeleteButton.addEventListener("click", function () {
      const selectedIds = getSelectedIds();
      if (selectedIds.length === 0) {
        alert("Please select at least one option.");
        return;
      }

      const formData = new FormData();
      formData.append("action", "wpdba_bulk_delete");
      formData.append("ids", JSON.stringify(selectedIds));
      formData.append("nonce", wpdba_ajax.nonce);

      fetch(ajaxUrl, {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            alert("Options deleted successfully!");
            location.reload();
          } else {
            alert("Failed to delete options: " + data.message);
          }
        })
        .catch((error) => console.error("Error:", error));
    });
  }

  // Function to handle autoload change
  function handleAutoloadChange(action) {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) {
      alert("Please select at least one option.");
      return;
    }

    const formData = new FormData();
    formData.append("action", "wpdba_bulk_autoload");
    formData.append("ids", JSON.stringify(selectedIds));
    formData.append("autoload", action);
    formData.append("nonce", wpdba_ajax.nonce);

    fetch(ajaxUrl, {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert(
            `Autoload successfully ${
              action === "enable" ? "enabled" : "disabled"
            }!`
          );
          location.reload();
        } else {
          alert("Failed to change autoload: " + data.message);
        }
      })
      .catch((error) => console.error("Error:", error));
  }
});

/****************************************
Add toggle button in the Tables section
****************************************/

document.addEventListener("DOMContentLoaded", function () {
  const toggleButtons = document.querySelectorAll(".wpdba-table-expand");

  toggleButtons.forEach((button) => {
    button.addEventListener("click", function () {
      // Select the details div relative to the clicked button
      const details = this.parentElement.querySelector(".details");

      // Toggle display of details
      if (details.style.display === "block") {
        details.style.display = "none"; // Collapse
        this.classList.remove("expanded");
      } else {
        details.style.display = "block"; // Expand
        this.classList.add("expanded");
      }
    });
  });
});
