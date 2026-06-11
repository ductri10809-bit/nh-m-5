/**
 * tim_kiem.js - Search with autocomplete suggestions (Google-style)
 * Waits for header to load since it's a dynamic component
 */

let timeout;
let allProducts = [];
let suggestionsList = null;

// Create suggestions dropdown in search-container
function createSuggestionsDropdown() {
  let dropdown = document.getElementById('search-suggestions');
  if (!dropdown) {
    dropdown = document.createElement('ul');
    dropdown.id = 'search-suggestions';
    dropdown.className = 'search-suggestions';
    const searchContainer = document.querySelector('.search-container');
    if (searchContainer) {
      searchContainer.appendChild(dropdown);
      console.log('✓ Suggestions dropdown created');
    }
  }
  return dropdown;
}

// Fetch all products once
async function taiTatCaSanPham() {
  try {
    const result = await Chung.goiApi('san_pham.php?limit=1000');
    if (result.success && result.data) {
      allProducts = result.data;
      console.log('✓ Loaded ' + allProducts.length + ' products for suggestions');
    }
  } catch (e) {
    console.error('✗ Loi tai san pham:', e);
  }
}

// Show suggestions from 1st character
function hienGoiY(keyword) {
  if (keyword.length === 0) {
    if (suggestionsList) {
      suggestionsList.innerHTML = '';
      suggestionsList.style.display = 'none';
    }
    return;
  }

  if (!suggestionsList) {
    suggestionsList = createSuggestionsDropdown();
  }

  const keywordLower = keyword.toLowerCase();
  const suggestions = allProducts
    .filter(p => p.ten.toLowerCase().includes(keywordLower))
    .slice(0, 8)
    .map(p => p.ten);

  if (suggestions.length > 0) {
    suggestionsList.innerHTML = suggestions
      .map(name => `
        <li data-product="${name}">
          <svg class="search-icon-suggestion" viewBox="0 0 24 24" width="16" height="16">
            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>
          </svg>
          <span class="suggestion-text">${name}</span>
        </li>
      `)
      .join('');
    suggestionsList.style.display = 'block';

    // Handle suggestion click
    suggestionsList.querySelectorAll('li').forEach(li => {
      li.addEventListener('click', () => {
        const input = document.getElementById('tim-kiem');
        if (input) input.value = li.dataset.product;
        suggestionsList.style.display = 'none';
        if (input) input.focus();
      });
    });
  } else {
    suggestionsList.innerHTML = '';
    suggestionsList.style.display = 'none';
  }
}

// Initialize search when input is ready
function initSearch() {
  const input = document.getElementById('tim-kiem');
  if (!input) {
    console.log('⏳ Waiting for tim-kiem input...');
    setTimeout(initSearch, 100);
    return;
  }

  console.log('✓ Search initialized');

  input.addEventListener('input', () => {
    const q = input.value.trim();
    hienGoiY(q);
    
    clearTimeout(timeout);
    timeout = setTimeout(() => {
      if (q.length >= 2) {
        document.dispatchEvent(new CustomEvent('searchQuery', { detail: { q } }));
      }
    }, 300);
  });

  input.addEventListener('focus', () => {
    const q = input.value.trim();
    if (q.length > 0) {
      hienGoiY(q);
    }
  });

  // Hide suggestions when clicking outside
  document.addEventListener('click', (e) => {
    if (!input.contains(e.target) && suggestionsList && !suggestionsList.contains(e.target)) {
      suggestionsList.style.display = 'none';
    }
  });
}

// Start when DOM ready
document.addEventListener('DOMContentLoaded', () => {
  taiTatCaSanPham();
  initSearch();
});

// Also watch for component loaded event (in case header loads after DOMContentLoaded)
document.addEventListener('componentLoaded', () => {
  if (!document.getElementById('tim-kiem')) {
    initSearch();
  }
});
