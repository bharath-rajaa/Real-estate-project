(function ($) {
    "use strict";

    // Spinner
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) {
                $('#spinner').removeClass('show');
            }
        }, 1);
    };
    spinner();


    // Initiate the wowjs
    new WOW().init();


    // Sticky Navbar
    $(window).scroll(function () {
        if ($(this).scrollTop() > 45) {
            $('.nav-bar').addClass('sticky-top');
        } else {
            $('.nav-bar').removeClass('sticky-top');
        }
    });


    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({ scrollTop: 0 }, 1500, 'easeInOutExpo');
        return false;
    });


    // Header carousel
    $(".header-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1500,
        items: 1,
        dots: true,
        loop: true,
        nav: true,
        navText: [
            '<i class="bi bi-chevron-left"></i>',
            '<i class="bi bi-chevron-right"></i>'
        ]
    });


    // Testimonials carousel
    $(".testimonial-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1000,
        margin: 24,
        dots: false,
        loop: true,
        nav: true,
        navText: [
            '<i class="bi bi-arrow-left"></i>',
            '<i class="bi bi-arrow-right"></i>'
        ],
        responsive: {
            0: {
                items: 1
            },
            992: {
                items: 2
            }
        }
    });

    // Property sidebar functionality
    // Property sidebar functionality
    $(document).ready(function () {
        // Load default content (Plots) on page load
        loadPropertyData('Plot');

        // Handle button clicks
        $('.property-btn').click(function () {
            var category = $(this).data('category');
            loadPropertyData(category);
        });

        function loadPropertyData(category) {
            console.log('Loading data for category:', category);
            $.getJSON('js/data.json', function (data) {
                console.log('Data loaded:', data);
                // Filter data based on category
                var filteredData = data.filter(function (item) {
                    return item.category === category;
                });
                console.log('Filtered data:', filteredData);

                // Generate HTML for the property content
                var propertyContent = '<h3>' + category + ' Properties</h3>';

                if (filteredData.length > 0) {
                    filteredData.forEach(function (property) {
                        propertyContent += '<div class="property-card mb-3 p-3 border">';
                        // Add property image if available
                        if (property.images && property.images.length > 0) {
                            propertyContent += '<div class="mb-3">';
                            propertyContent += '<img src="' + property.images[0] + '" class="img-fluid rounded" alt="' + property.name + '" style="width: 100%; height: 200px; object-fit: cover;">';
                            propertyContent += '</div>';
                        }
                        propertyContent += '<h5>' + property.name + '</h5>';
                        propertyContent += '<p><strong>Location:</strong> ' + property.location + '</p>';
                        propertyContent += '<p><strong>Price:</strong> ₹' + property.price.toLocaleString() + '</p>';
                        propertyContent += '<p><strong>Size:</strong> ' + property.sqft + ' sqft</p>';
                        propertyContent += '<p><strong>Status:</strong> ' + property.status + '</p>';
                        propertyContent += '<p><strong>Features:</strong> ' + property.features.join(', ') + '</p>';
                        propertyContent += '<p>' + property.description + '</p>';
                        propertyContent += '</div>';
                    });
                } else {
                    propertyContent += '<p>No properties found in this category.</p>';
                }

                // Set the property content
                $('#propertyContent').html(propertyContent);
            }).fail(function (jqxhr, textStatus, error) {
                console.error('Error loading property data:', textStatus, error);
                $('#propertyContent').html('<p class="text-danger">Error loading property data. Please try again later.</p>');
            });
        }
    });


    // Fetch data from data.json and initialize
    let allProperties = [];

    async function fetchProperties() {
        try {
            const response = await fetch('js/data.json');
            const data = await response.json();

            // Combine all property categories
            allProperties = [
                ...(data.plots || []).map(p => ({
                    id: p.id,
                    category: 'plots',
                    title: p.name,
                    location: p.location,
                    description: p.description,
                    image: p.images[0] || '',
                    features: { bedrooms: 'N/A', bathrooms: 'N/A', area: p.sqft + ' sqft' },
                    price: '₹' + (p.price / 100000).toFixed(1) + ' Lakhs',
                    emi: p.emi ? 'EMI: ₹' + p.emi.toLocaleString() : '',
                    status: p.status,
                    fullData: p
                })),
                ...(data.flats || []).map(p => ({
                    id: p.id,
                    category: 'flats',
                    title: p.name,
                    location: p.location,
                    description: p.description,
                    image: p.images[0] || '',
                    features: { bedrooms: p.bhk || 'N/A', bathrooms: '2', area: p.sqft + ' sqft' },
                    price: '₹' + (p.price / 100000).toFixed(1) + ' Lakhs',
                    emi: p.emi ? 'EMI: ₹' + p.emi.toLocaleString() : '',
                    status: p.status,
                    fullData: p
                })),
                ...(data.resale_properties || []).map(p => ({
                    id: p.id,
                    category: 'resale',
                    title: p.name,
                    location: p.location,
                    description: p.description,
                    image: p.images[0] || '',
                    features: { bedrooms: p.bhk || 'N/A', bathrooms: '2', area: p.sqft + ' sqft' },
                    price: '₹' + (p.price / 100000).toFixed(1) + ' Lakhs',
                    emi: p.emi ? 'EMI: ₹' + p.emi.toLocaleString() : '',
                    status: p.status,
                    fullData: p
                })),
                ...(data.bank_properties || []).map(p => ({
                    id: p.id,
                    category: 'bank',
                    title: p.name,
                    location: p.location,
                    description: p.description,
                    image: p.images[0] || '',
                    features: { bedrooms: p.bhk || 'N/A', bathrooms: '2', area: p.sqft + ' sqft' },
                    price: '₹' + (p.price / 100000).toFixed(1) + ' Lakhs',
                    emi: p.emi ? 'EMI: ₹' + p.emi.toLocaleString() : '',
                    status: p.status,
                    fullData: p
                })),
                ...(data.acre || []).map(p => ({
                    id: p.id,
                    category: 'acre',
                    title: p.name,
                    location: p.location,
                    description: p.description,
                    image: p.images[0] || '',
                    features: { bedrooms: p.bhk || 'N/A', bathrooms: 'N/A', area: p.sqft + ' sqft' },
                    price: '₹' + (p.price / 100000).toFixed(1) + ' Lakhs',
                    emi: p.emi ? 'EMI: ₹' + p.emi.toLocaleString() : '',
                    status: p.status,
                    fullData: p
                }))
            ];

            // Update category counts in sidebar
            updateCategoryCounts(data);

            // Initialize by displaying all properties
            displayProperties(allProperties);
        } catch (error) {
            console.error('Error fetching properties:', error);
        }
    }

    // Update sidebar category counts
    function updateCategoryCounts(data) {
        const counts = {
            all: 0,
            plots: (data.plots || []).length,
            flats: (data.flats || []).length,
            resale: (data.resale_properties || []).length,
            bank: (data.bank_properties || []).length,
            acre: (data.acre || []).length
        };
        counts.all = counts.plots + counts.flats + counts.resale + counts.bank + counts.acre;

        // Update counts in the sidebar
        document.querySelectorAll('.category-item').forEach(item => {
            const category = item.getAttribute('data-category');
            const countSpan = item.querySelector('.category-count');
            if (countSpan && counts[category] !== undefined) {
                countSpan.textContent = counts[category];
            }
        });
    }

    // DOM elements
    const propertiesContainer = document.getElementById('properties-container');
    const categoryItems = document.querySelectorAll('.category-item');
    const countElement = document.getElementById('count');

    // Add click event listeners to category items
    categoryItems.forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            // Remove active class from all category items
            categoryItems.forEach(cat => cat.classList.remove('active'));

            // Add active class to clicked category item
            this.classList.add('active');

            // Get the category to filter by
            const category = this.getAttribute('data-category');

            // Filter properties based on selected category
            let filteredProperties;
            if (category === 'all') {
                filteredProperties = allProperties;
            } else {
                filteredProperties = allProperties.filter(property => property.category === category);
            }

            // Display filtered properties
            displayProperties(filteredProperties);

            // Update property count
            if (countElement) {
                countElement.textContent = filteredProperties.length;
            }
        });
    });

    // Function to display properties
    function displayProperties(propertiesArray) {
        // Clear the container
        propertiesContainer.innerHTML = '';

        // Check if there are properties to display
        if (propertiesArray.length === 0) {
            propertiesContainer.innerHTML = `
                    <div class="no-results">
                        <i class="fas fa-search"></i>
                        <h3>No Properties Found</h3>
                        <p>We couldn't find any properties in this category. Please try another category.</p>
                    </div>
                `;
            return;
        }

        // Create property cards for each property
        propertiesArray.forEach(property => {
            const propertyCard = document.createElement('div');
            propertyCard.className = 'property-card';
            propertyCard.innerHTML = `
                    <img src="${property.image}" alt="${property.title}" class="property-image">
                    <div class="property-info">
                        <h3 class="property-title">${property.title}</h3>
                        <div class="property-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${property.location}</span>
                        </div>
                        <p class="property-description">${property.description}</p>
                        <div class="property-features">
                            <div class="property-feature">
                                <i class="fas fa-bed"></i>
                                <span>${property.features.bedrooms}</span>
                            </div>
                            <div class="property-feature">
                                <i class="fas fa-bath"></i>
                                <span>${property.features.bathrooms} Bath</span>
                            </div>
                            <div class="property-feature">
                                <i class="fas fa-arrows-alt"></i>
                                <span>${property.features.area}</span>
                            </div>
                        </div>
                        <div class="property-price">${property.price}</div>
                        ${property.emi ? `<div class="property-emi">${property.emi}/month</div>` : ''}
                        <div class="property-actions">
                            <a href="#" class="btn btn-primary">View Details</a>
                            <a href="#" class="btn btn-secondary">Contact Agent</a>
                        </div>
                    </div>
                `;

            propertiesContainer.appendChild(propertyCard);
        });
    }

    // Fetch and initialize properties on page load
    fetchProperties();

    // Handle URL category parameter for direct navigation
    function handleCategoryFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        const categoryParam = urlParams.get('category');

        if (categoryParam) {
            // Auto-select the matching sidebar category
            const categoryItem = document.querySelector(`.category-item[data-category="${categoryParam}"]`);
            if (categoryItem) {
                categoryItem.click();
            }
            
            // Update breadcrumb if exists
            const breadcrumb = document.querySelector('.breadcrumb-item.active');
            if (breadcrumb) {
                const categoryNames = {
                    'plots': 'Plots',
                    'flats': 'Flats',
                    'acre': 'Acre',
                    'resale': 'Resale Properties',
                    'bank': 'Bank Properties'
                };
                breadcrumb.textContent = categoryNames[categoryParam] || categoryParam;
            }
        }
    }

    // Run URL parameter handling after a short delay to ensure DOM is ready
    setTimeout(handleCategoryFromURL, 100);

})(jQuery);
