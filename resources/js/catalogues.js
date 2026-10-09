const library = document.querySelector('[data-catalogue-library]');

if (library) {
    const filters = Object.fromEntries(
        [...library.querySelectorAll('[data-catalogue-filter]')].map((filter) => [filter.dataset.catalogueFilter, filter]),
    );
    const cards = [...library.querySelectorAll('[data-catalogue-card]')];
    const categoryGroups = [...library.querySelectorAll('[data-catalogue-category-group]')];
    const count = library.querySelector('[data-catalogue-count]');
    const emptyState = library.querySelector('[data-catalogue-empty]');

    function updateCatalogues() {
        const selectedBrand = filters.brand.value;
        const selectedCategory = filters.category.value;
        const selectedConcept = filters.concept.value;
        let visibleCount = 0;

        cards.forEach((card) => {
            const concepts = card.dataset.conceptIds.split(' ').filter(Boolean);
            const isVisible = (!selectedBrand || card.dataset.brandId === selectedBrand)
                && (!selectedCategory || card.dataset.categoryId === selectedCategory)
                && (!selectedConcept || concepts.includes(selectedConcept));

            card.hidden = !isVisible;
            visibleCount += Number(isVisible);
        });

        categoryGroups.forEach((group) => {
            group.hidden = !group.querySelector('[data-catalogue-card]:not([hidden])');
        });

        count.textContent = `${visibleCount} ${visibleCount === 1 ? 'catalogue' : 'catalogues'}`;
        emptyState.hidden = visibleCount > 0;
    }

    Object.values(filters).forEach((filter) => filter.addEventListener('change', updateCatalogues));
    library.querySelector('[data-catalogue-reset]')?.addEventListener('click', () => {
        Object.values(filters).forEach((filter) => {
            filter.value = '';
        });
        updateCatalogues();
    });
}
