/**
 * Searchable University Select Tool
 * Judiciary Internship Management Portal (JIMP)
 *
 * Enhances standard <select> elements into accessible, searchable
 * comboboxes with real-time filtering, keyboard navigation, and
 * streamlined "Add unlisted institution" workflows.
 */

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

export function initSearchableUniversitySelect(selectEl) {
    if (!selectEl || selectEl.dataset.searchableInitialized === 'true') {
        return;
    }

    selectEl.dataset.searchableInitialized = 'true';

    // Parse options from the native select
    function parseOptions() {
        const items = [];
        const optionEls = selectEl.querySelectorAll('option');

        optionEls.forEach(opt => {
            if (opt.value === '' && opt.disabled) {
                return; // placeholder option
            }

            const isOther = opt.value === '__other__' || opt.dataset.isOther === 'true';
            const location = opt.dataset.location || '';
            const rawText = opt.textContent.trim();

            // Extract location from text if not in data-location (e.g. "Name (Location)")
            let cleanName = rawText;
            let cleanLoc = location;
            if (!cleanLoc && !isOther) {
                const match = rawText.match(/^(.*?)\s*\((.*?)\)$/);
                if (match) {
                    cleanName = match[1].trim();
                    cleanLoc = match[2].trim();
                }
            }

            items.push({
                value: opt.value,
                text: cleanName,
                location: cleanLoc,
                fullText: rawText,
                isOther: isOther,
                selected: opt.selected,
                element: opt,
            });
        });

        return items;
    }

    let options = parseOptions();

    // Hide native select visually while keeping it form-submittable
    selectEl.classList.add('searchable-native-hidden');
    selectEl.setAttribute('tabindex', '-1');
    selectEl.setAttribute('aria-hidden', 'true');

    // Create custom wrapper
    const wrapper = document.createElement('div');
    wrapper.className = 'searchable-select-wrapper';

    // Insert wrapper right after native select
    selectEl.parentNode.insertBefore(wrapper, selectEl.nextSibling);

    // Build the trigger button
    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.className = 'searchable-select-trigger';
    trigger.setAttribute('aria-haspopup', 'listbox');
    trigger.setAttribute('aria-expanded', 'false');

    // Build the dropdown panel
    const dropdown = document.createElement('div');
    dropdown.className = 'searchable-select-dropdown';
    dropdown.setAttribute('role', 'listbox');
    dropdown.style.display = 'none';

    dropdown.innerHTML = `
        <div class="searchable-select-search-row">
            <span class="searchable-select-search-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </span>
            <input
                type="text"
                class="searchable-select-input"
                placeholder="Type to search university, campus, town..."
                autocomplete="off"
                spellcheck="false"
                aria-label="Search universities"
            >
            <button type="button" class="searchable-select-search-clear" title="Clear search" aria-label="Clear search" style="display: none;">
                &times;
            </button>
        </div>
        <div class="searchable-select-meta-bar">
            <span class="searchable-select-count"></span>
            <span class="searchable-select-tip">Use ↑ ↓ to navigate</span>
        </div>
        <div class="searchable-select-options" tabindex="-1"></div>
        <div class="searchable-select-footer">
            <button type="button" class="searchable-select-add-unlisted-btn">
                <span class="unlisted-icon">➕</span>
                <span class="unlisted-text">
                    <strong>Institution not in accredited list?</strong>
                    <small>Click to add your institution for HR approval</small>
                </span>
            </button>
        </div>
    `;

    wrapper.appendChild(trigger);
    wrapper.appendChild(dropdown);

    const searchInput = dropdown.querySelector('.searchable-select-input');
    const searchClear = dropdown.querySelector('.searchable-select-search-clear');
    const optionsContainer = dropdown.querySelector('.searchable-select-options');
    const countLabel = dropdown.querySelector('.searchable-select-count');
    const addUnlistedBtn = dropdown.querySelector('.searchable-select-add-unlisted-btn');

    let isOpen = false;
    let highlightedIndex = -1;
    let filteredOptions = [];

    // Find custom institution inputs on the page if they exist
    function getCustomNameInput() {
        return document.getElementById('custom_institution_name') ||
               document.getElementById('dash_custom_name') ||
               document.querySelector('input[name="custom_institution_name"]');
    }

    function getCustomLocationInput() {
        return document.getElementById('custom_institution_location') ||
               document.getElementById('dash_custom_location') ||
               document.querySelector('input[name="custom_institution_location"]');
    }

    // Update the trigger display according to the currently selected option
    function updateTriggerDisplay() {
        const selectedVal = selectEl.value;
        const selectedOpt = options.find(o => o.value === selectedVal);

        if (selectedOpt && selectedVal !== '') {
            trigger.classList.remove('has-error');
            trigger.classList.add('has-value');

            if (selectedOpt.isOther) {
                trigger.innerHTML = `
                    <span class="searchable-select-lead-icon icon-custom" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none">
                            <path d="M12 2l10 6-10 6-10-6 10-6z"></path>
                            <path d="M4 14l8 5 8-5"></path>
                        </svg>
                    </span>
                    <span class="searchable-select-label-text">
                        <span class="selected-uni-name">➕ Unlisted Institution (Pending HR)</span>
                        <span class="selected-uni-badge badge-custom">Custom Request</span>
                    </span>
                    <span class="searchable-select-action-icons">
                        <span class="searchable-select-reset-btn" title="Clear selection" aria-label="Clear selection">&times;</span>
                        <span class="searchable-select-chevron" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </span>
                    </span>
                `;
            } else {
                const locBadge = selectedOpt.location
                    ? `<span class="selected-uni-location" title="${escapeHtml(selectedOpt.location)}">📍 ${escapeHtml(selectedOpt.location)}</span>`
                    : '';

                trigger.innerHTML = `
                    <span class="searchable-select-lead-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                        </svg>
                    </span>
                    <span class="searchable-select-label-text">
                        <span class="selected-uni-name">${escapeHtml(selectedOpt.text)}</span>
                        ${locBadge}
                    </span>
                    <span class="searchable-select-action-icons">
                        <span class="searchable-select-reset-btn" title="Clear selection" aria-label="Clear selection">&times;</span>
                        <span class="searchable-select-chevron" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </span>
                    </span>
                `;
            }
        } else {
            trigger.classList.remove('has-value');
            trigger.innerHTML = `
                <span class="searchable-select-lead-icon icon-placeholder" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                        <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                    </svg>
                </span>
                <span class="searchable-select-label-text">
                    <span class="placeholder-text">🔍 Search or select university...</span>
                </span>
                <span class="searchable-select-action-icons">
                    <span class="searchable-select-chevron" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </span>
                </span>
            `;
        }

        // Attach event listener to reset button if present
        const resetBtn = trigger.querySelector('.searchable-select-reset-btn');
        if (resetBtn) {
            resetBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                selectValue('');
            });
        }
    }

    // Select a value and fire appropriate events
    function selectValue(val, customNameValue = null) {
        selectEl.value = val;
        selectEl.dispatchEvent(new Event('change', { bubbles: true }));

        if (val === '__other__' && customNameValue) {
            const customInput = getCustomNameInput();
            if (customInput) {
                customInput.value = customNameValue;
                customInput.dispatchEvent(new Event('input', { bubbles: true }));
                const locInput = getCustomLocationInput();
                if (locInput) {
                    locInput.focus();
                } else {
                    customInput.focus();
                }
            }
        }

        updateTriggerDisplay();
        closeDropdown();
        trigger.focus();
    }

    // Highlight search match characters
    function highlightMatch(text, query) {
        if (!query) return escapeHtml(text);
        const escapedQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp(`(${escapedQuery})`, 'gi');
        return escapeHtml(text).replace(regex, '<mark class="select-match-mark">$1</mark>');
    }

    // Render filtered options in the dropdown list
    function renderOptions(query = '') {
        const q = query.trim().toLowerCase();
        options = parseOptions(); // refresh in case options changed

        // Filter accredited options (exclude other)
        const accredited = options.filter(o => !o.isOther);

        if (!q) {
            filteredOptions = accredited;
        } else {
            const terms = q.split(/\s+/).filter(Boolean);
            filteredOptions = accredited.filter(opt => {
                const haystack = `${opt.text} ${opt.location}`.toLowerCase();
                return terms.every(t => haystack.includes(t));
            });
        }

        // Update count label
        if (q) {
            countLabel.textContent = `${filteredOptions.length} ${filteredOptions.length === 1 ? 'match' : 'matches'} found`;
        } else {
            countLabel.textContent = `${filteredOptions.length} accredited universities`;
        }

        optionsContainer.innerHTML = '';

        if (filteredOptions.length === 0) {
            const emptyEl = document.createElement('div');
            emptyEl.className = 'searchable-select-empty';
            emptyEl.innerHTML = `
                <div class="empty-icon">🏛️</div>
                <div class="empty-title">No accredited universities match "<strong>${escapeHtml(query)}</strong>"</div>
                <div class="empty-desc">Your university might not be accredited yet on the Judiciary directory.</div>
                <button type="button" class="empty-add-btn">
                    <span>➕ Add "<strong>${escapeHtml(query)}</strong>" as unlisted institution</span>
                </button>
            `;

            const emptyAddBtn = emptyEl.querySelector('.empty-add-btn');
            emptyAddBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                selectValue('__other__', query);
            });

            optionsContainer.appendChild(emptyEl);
            highlightedIndex = -1;
            return;
        }

        // Render each matching university
        filteredOptions.forEach((opt, idx) => {
            const isSelected = selectEl.value === opt.value;
            const item = document.createElement('div');
            item.className = `searchable-select-item ${isSelected ? 'is-selected' : ''}`;
            item.setAttribute('role', 'option');
            item.setAttribute('aria-selected', isSelected ? 'true' : 'false');
            item.dataset.index = idx;

            const highlightedName = highlightMatch(opt.text, q);
            const highlightedLoc = opt.location ? highlightMatch(opt.location, q) : '';

            item.innerHTML = `
                <div class="uni-item-info">
                    <span class="uni-item-name">${highlightedName}</span>
                    ${highlightedLoc ? `<span class="uni-item-location">📍 ${highlightedLoc}</span>` : ''}
                </div>
                ${isSelected ? `
                    <span class="uni-item-check" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </span>
                ` : ''}
            `;

            item.addEventListener('click', (e) => {
                e.stopPropagation();
                selectValue(opt.value);
            });

            item.addEventListener('mouseenter', () => {
                setHighlightedIndex(idx);
            });

            optionsContainer.appendChild(item);
        });

        // Set initial highlighted item
        const selectedIndex = filteredOptions.findIndex(o => o.value === selectEl.value);
        setHighlightedIndex(selectedIndex >= 0 ? selectedIndex : 0);
    }

    function setHighlightedIndex(index) {
        const items = optionsContainer.querySelectorAll('.searchable-select-item');
        items.forEach(it => it.classList.remove('is-highlighted'));

        if (index >= 0 && index < items.length) {
            highlightedIndex = index;
            const target = items[index];
            target.classList.add('is-highlighted');

            // Scroll into view if needed
            const containerTop = optionsContainer.scrollTop;
            const containerBottom = containerTop + optionsContainer.clientHeight;
            const itemTop = target.offsetTop;
            const itemBottom = itemTop + target.clientHeight;

            if (itemTop < containerTop) {
                optionsContainer.scrollTop = itemTop;
            } else if (itemBottom > containerBottom) {
                optionsContainer.scrollTop = itemBottom - optionsContainer.clientHeight;
            }
        } else {
            highlightedIndex = -1;
        }
    }

    function openDropdown() {
        if (isOpen) return;
        isOpen = true;
        dropdown.style.display = 'flex';
        trigger.setAttribute('aria-expanded', 'true');
        trigger.classList.add('is-open');

        // Render options fresh
        searchInput.value = '';
        searchClear.style.display = 'none';
        renderOptions('');

        // Focus search input on next tick
        setTimeout(() => {
            searchInput.focus();
            // Scroll selected into view
            const selectedItem = optionsContainer.querySelector('.is-selected');
            if (selectedItem) {
                selectedItem.scrollIntoView({ block: 'nearest' });
            }
        }, 30);
    }

    function closeDropdown() {
        if (!isOpen) return;
        isOpen = false;
        dropdown.style.display = 'none';
        trigger.setAttribute('aria-expanded', 'false');
        trigger.classList.remove('is-open');
    }

    function toggleDropdown() {
        if (isOpen) {
            closeDropdown();
        } else {
            openDropdown();
        }
    }

    // Trigger button events
    trigger.addEventListener('click', (e) => {
        e.preventDefault();
        toggleDropdown();
    });

    trigger.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowDown' || e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            openDropdown();
        }
    });

    // Search input events
    searchInput.addEventListener('input', () => {
        const query = searchInput.value;
        searchClear.style.display = query.length > 0 ? 'inline-flex' : 'none';
        renderOptions(query);
    });

    searchClear.addEventListener('click', (e) => {
        e.stopPropagation();
        searchInput.value = '';
        searchClear.style.display = 'none';
        renderOptions('');
        searchInput.focus();
    });

    searchInput.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (filteredOptions.length > 0) {
                const nextIndex = (highlightedIndex + 1) % filteredOptions.length;
                setHighlightedIndex(nextIndex);
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (filteredOptions.length > 0) {
                const prevIndex = (highlightedIndex - 1 + filteredOptions.length) % filteredOptions.length;
                setHighlightedIndex(prevIndex);
            }
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (highlightedIndex >= 0 && highlightedIndex < filteredOptions.length) {
                selectValue(filteredOptions[highlightedIndex].value);
            } else if (filteredOptions.length === 0 && searchInput.value.trim()) {
                selectValue('__other__', searchInput.value.trim());
            }
        } else if (e.key === 'Escape') {
            e.preventDefault();
            closeDropdown();
            trigger.focus();
        } else if (e.key === 'Tab') {
            closeDropdown();
        }
    });

    // Pinned footer action "Institution not in list"
    addUnlistedBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        const pendingQuery = searchInput.value.trim();
        selectValue('__other__', pendingQuery || null);
    });

    // Close on outside click
    document.addEventListener('click', (e) => {
        if (!wrapper.contains(e.target)) {
            closeDropdown();
        }
    });

    // Handle HTML5 validation errors on hidden native select
    selectEl.addEventListener('invalid', (e) => {
        e.preventDefault();
        trigger.classList.add('has-error');
        trigger.focus();
    });

    // Sync if select value is modified programmatically
    selectEl.addEventListener('change', () => {
        updateTriggerDisplay();
    });

    // Initial render
    updateTriggerDisplay();
}

/**
 * Automatically initializes all searchable university selects on the page.
 */
export function initAllSearchableUniversitySelects() {
    const selects = document.querySelectorAll(
        'select.searchable-university-select, select#dash_institution_select, select#institution_select'
    );
    selects.forEach(selectEl => {
        initSearchableUniversitySelect(selectEl);
    });
}

// Auto-run when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAllSearchableUniversitySelects);
} else {
    initAllSearchableUniversitySelects();
}

// Expose globally for convenience
if (typeof window !== 'undefined') {
    window.initSearchableUniversitySelect = initSearchableUniversitySelect;
    window.initAllSearchableUniversitySelects = initAllSearchableUniversitySelects;
}
