/**
 * Global Search Functionality - Real-time Asynchronous Search
 * Search across different pages and content with live API
 */

(function() {
    'use strict';

    const API_ENDPOINT = 'api/search.php';
    let searchController = null;

    // Perform asynchronous search
    async function performSearch(query) {
        if (!query || query.trim().length < 2) {
            return {
                success: true,
                results: [],
                message: 'Query too short'
            };
        }

        // Cancel previous request if exists
        if (searchController) {
            searchController.abort();
        }

        // Create new AbortController for this request
        searchController = new AbortController();

        try {
            const url = `${API_ENDPOINT}?q=${encodeURIComponent(query)}&limit=10`;
            console.log('Searching:', url);
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                },
                signal: searchController.signal
            });

            console.log('Response status:', response.status);

            if (!response.ok) {
                const errorText = await response.text();
                console.error('Search failed:', errorText);
                
                // Try to parse as JSON for better error message
                try {
                    const errorData = JSON.parse(errorText);
                    console.error('Error details:', errorData);
                    throw new Error(errorData.message || 'Search request failed: ' + response.status);
                } catch (parseError) {
                    throw new Error('Search request failed: ' + response.status + ' - ' + errorText.substring(0, 200));
                }
            }

            const data = await response.json();
            console.log('Search results:', data);
            return data;

        } catch (error) {
            if (error.name === 'AbortError') {
                console.log('Search request aborted');
                return { success: false, results: [], aborted: true };
            }
            
            console.error('Search error:', error);
            return {
                success: false,
                results: [],
                message: error.message
            };
        }
    }

    // Highlight matching text
    function highlightMatch(text, query) {
        if (!text || !query) return text;
        
        const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
        return text.replace(regex, '<mark class="search-highlight">$1</mark>');
    }

    // Render search results
    function renderResults(results, container, isLoading = false, query = '') {
        if (isLoading) {
            container.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-2 mb-0">Mencari...</p>
                </div>
            `;
            return;
        }

        if (!results || results.length === 0) {
            container.innerHTML = `
                <div class="text-center py-4">
                    <i class="ti ti-search-off" style="font-size: 48px; color: #ccc;"></i>
                    <p class="text-muted mt-2 mb-0">Tidak ada hasil ditemukan</p>
                    <small class="text-muted">Coba kata kunci lain</small>
                </div>
            `;
            return;
        }

        let html = '';
        
        // Add result count indicator
        if (results.length > 5) {
            html += `
                <div class="search-results-count">
                    <i class="ti ti-list-search me-1"></i>
                    Menampilkan ${results.length} hasil untuk "<strong>${query}</strong>" - scroll untuk melihat semua
                </div>
            `;
        } else if (query) {
            html += `
                <div class="search-results-count">
                    <i class="ti ti-search me-1"></i>
                    Hasil pencarian untuk "<strong>${query}</strong>"
                </div>
            `;
        }
        
        html += '<div class="list-group">';
        
        results.forEach(result => {
            const categoryBadge = result.category ? 
                `<span class="badge bg-light text-dark ms-2 fw-normal">${result.category}</span>` : '';
            
            const typeBadge = getTypeBadge(result.type);
            
            // Highlight matching text in name and description
            const highlightedName = highlightMatch(result.name, query);
            const highlightedDescription = highlightMatch(result.description || '', query);
            
            html += `
                <a href="${result.url}" class="list-group-item list-group-item-action search-result-item">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s ${getTypeColor(result.type)}">
                                <i class="${result.icon}"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">
                                ${highlightedName} 
                                ${typeBadge}
                                ${categoryBadge}
                            </h6>
                            <small class="text-muted">${highlightedDescription}</small>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="ti ti-chevron-right"></i>
                        </div>
                    </div>
                </a>
            `;
        });
        
        html += '</div>';
        container.innerHTML = html;
        console.log('Rendered', results.length, 'results');
        
        // Scroll to top when new results are rendered
        container.scrollTop = 0;
    }

    // Get type badge
    function getTypeBadge(type) {
        const badges = {
            'product': '<span class="badge bg-success ms-2">Produk</span>',
            'topping': '<span class="badge bg-info ms-2">Topping</span>',
            'category': '<span class="badge bg-warning ms-2">Kategori</span>',
            'order': '<span class="badge bg-primary ms-2">Order</span>',
            'user': '<span class="badge bg-secondary ms-2">User</span>',
            'expense': '<span class="badge bg-danger ms-2">Expense</span>',
            'page': '<span class="badge bg-dark ms-2">Page</span>'
        };
        return badges[type] || '';
    }

    // Get type color
    function getTypeColor(type) {
        const colors = {
            'product': 'bg-light-success',
            'topping': 'bg-light-info',
            'category': 'bg-light-warning',
            'order': 'bg-light-primary',
            'user': 'bg-light-secondary',
            'expense': 'bg-light-danger',
            'page': 'bg-light-dark'
        };
        return colors[type] || 'bg-light-secondary';
    }

    // Initialize search for desktop
    function initDesktopSearch() {
        const searchForm = document.querySelector('.header-search');
        if (!searchForm) return;

        const searchInput = searchForm.querySelector('input[type="search"]');
        const searchButton = searchForm.querySelector('.btn-search');
        
        if (!searchInput) return;

        // Create search results dropdown
        const resultsDropdown = document.createElement('div');
        resultsDropdown.className = 'search-results-dropdown';
        resultsDropdown.style.cssText = `
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            max-height: 450px;
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1050;
            display: none;
            margin-top: 8px;
            border: 1px solid #e0e0e0;
        `;
        searchForm.style.position = 'relative';
        searchForm.appendChild(resultsDropdown);

        // Handle input with debounce
        let searchTimeout;
        searchInput.addEventListener('input', async function() {
            clearTimeout(searchTimeout);
            const query = this.value;

            if (query.trim().length < 2) {
                resultsDropdown.style.display = 'none';
                return;
            }

            // Show loading state
            resultsDropdown.style.display = 'block';
            renderResults([], resultsDropdown, true);

            searchTimeout = setTimeout(async () => {
                const data = await performSearch(query);
                
                if (!data.aborted) {
                    if (data.success && data.results) {
                        renderResults(data.results, resultsDropdown, false, query);
                    } else {
                        renderResults([], resultsDropdown, false, query);
                    }
                    resultsDropdown.style.display = 'block';
                }
            }, 300); // 300ms debounce
        });

        // Handle search button click
        if (searchButton) {
            searchButton.addEventListener('click', async function(e) {
                e.preventDefault();
                const query = searchInput.value;
                if (query.trim().length >= 2) {
                    renderResults([], resultsDropdown, true);
                    resultsDropdown.style.display = 'block';
                    
                    const data = await performSearch(query);
                    if (data.success && data.results) {
                        renderResults(data.results, resultsDropdown, false, query);
                    } else {
                        renderResults([], resultsDropdown, false, query);
                    }
                }
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchForm.contains(e.target)) {
                resultsDropdown.style.display = 'none';
            }
        });

        // Handle Enter key
        searchInput.addEventListener('keypress', async function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const data = await performSearch(this.value);
                if (data.success && data.results && data.results.length > 0) {
                    window.location.href = data.results[0].url;
                }
            }
        });

        // Handle focus
        searchInput.addEventListener('focus', function() {
            if (this.value.trim().length >= 2 && resultsDropdown.innerHTML) {
                resultsDropdown.style.display = 'block';
            }
        });
    }

    // Initialize search for mobile
    function initMobileSearch() {
        const mobileSearchForm = document.querySelector('.drp-search form');
        if (!mobileSearchForm) return;

        const searchInput = mobileSearchForm.querySelector('input[type="search"]');
        if (!searchInput) return;

        // Create search results container
        const resultsContainer = document.createElement('div');
        resultsContainer.className = 'mobile-search-results px-3 pb-3';
        resultsContainer.style.cssText = `
            max-height: 350px;
            overflow-y: auto;
            overflow-x: hidden;
        `;
        mobileSearchForm.parentElement.appendChild(resultsContainer);

        // Handle input with debounce
        let searchTimeout;
        searchInput.addEventListener('input', async function() {
            clearTimeout(searchTimeout);
            const query = this.value;

            if (query.trim().length < 2) {
                resultsContainer.innerHTML = '';
                return;
            }

            // Show loading state
            renderResults([], resultsContainer, true);

            searchTimeout = setTimeout(async () => {
                const data = await performSearch(query);
                
                if (!data.aborted) {
                    if (data.success && data.results) {
                        renderResults(data.results, resultsContainer, false, query);
                    } else {
                        renderResults([], resultsContainer, false, query);
                    }
                }
            }, 300); // 300ms debounce
        });

        // Handle Enter key
        searchInput.addEventListener('keypress', async function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const data = await performSearch(this.value);
                if (data.success && data.results && data.results.length > 0) {
                    window.location.href = data.results[0].url;
                }
            }
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initDesktopSearch();
            initMobileSearch();
        });
    } else {
        initDesktopSearch();
        initMobileSearch();
    }

    // Add CSS for search results
    const style = document.createElement('style');
    style.textContent = `
        .search-result-item {
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        .search-result-item:hover {
            background-color: #f8f9fa !important;
            transform: translateX(4px);
            border-left-color: #4680ff;
        }
        
        /* Highlight matching text */
        .search-highlight {
            background-color: #fff3cd;
            color: #856404;
            font-weight: 600;
            padding: 2px 4px;
            border-radius: 3px;
            border: 1px solid #ffc107;
        }
        
        .search-results-dropdown::-webkit-scrollbar,
        .mobile-search-results::-webkit-scrollbar {
            width: 8px;
        }
        .search-results-dropdown::-webkit-scrollbar-track,
        .mobile-search-results::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        .search-results-dropdown::-webkit-scrollbar-thumb,
        .mobile-search-results::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        .search-results-dropdown::-webkit-scrollbar-thumb:hover,
        .mobile-search-results::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Smooth scroll */
        .search-results-dropdown,
        .mobile-search-results {
            scroll-behavior: smooth;
        }
        
        /* Custom scrollbar for Firefox */
        .search-results-dropdown,
        .mobile-search-results {
            scrollbar-width: thin;
            scrollbar-color: #888 #f1f1f1;
        }
        
        /* Result count indicator */
        .search-results-count {
            position: sticky;
            top: 0;
            background: linear-gradient(to bottom, #f8f9fa 0%, #f8f9fa 80%, transparent 100%);
            padding: 8px 15px;
            font-size: 12px;
            color: #666;
            border-bottom: 1px solid #e0e0e0;
            z-index: 10;
        }
        
        .search-results-count strong {
            color: #4680ff;
        }
    `;
    document.head.appendChild(style);

})();
