<?php
require_once __DIR__ . '/../includes/db_config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

include __DIR__ . '/../templates/header.php';

// Get all shows
$shows = getAllShows();

// Get genres for filter
$genres = [];
foreach ($shows as $show) {
    if (!empty($show['genre']) && !in_array($show['genre'], $genres)) {
        $genres[] = $show['genre'];
    }
}
?>

<!-- Page Header -->
<div class="bg-light py-5">
    <div class="container">
        <h1 class="display-4">Current Shows</h1>
        <p class="lead">Explore our exciting lineup of shows and live performances.</p>
    </div>
</div>

<div class="container py-5">
    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="input-group">
                <input type="text" id="searchInput" class="form-control" placeholder="Search shows...">
                <button class="btn btn-primary" type="button" id="searchBtn">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
        <div class="col-md-4">
            <select id="genreFilter" class="form-select">
                <option value="">All Genres</option>
                <?php foreach ($genres as $genre): ?>
                    <option value="<?php echo htmlspecialchars($genre); ?>"><?php echo htmlspecialchars($genre); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    
    <!-- Shows Grid -->
    <div class="row" id="showsContainer">
        <?php if (empty($shows)): ?>
            <div class="col-12 text-center py-5">
                <h3>No shows available at this time.</h3>
                <p>Please check back later for upcoming performances.</p>
            </div>
        <?php else: ?>
            <?php foreach ($shows as $show): ?>
                <div class="col-md-4 mb-4 show-item" data-genre="<?php echo htmlspecialchars($show['genre']); ?>">
                    <div class="card h-100">
                        <?php if (!empty($show['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($show['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($show['title']); ?>">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-theater-masks fa-4x text-muted"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($show['title']); ?></h5>
                            <p class="card-text"><?php echo substr(htmlspecialchars($show['description']), 0, 120); ?>...</p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-primary"><?php echo htmlspecialchars($show['genre']); ?></span>
                                <span><i class="fas fa-clock me-1"></i> <?php echo $show['duration']; ?> mins</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted"><?php echo htmlspecialchars($show['language']); ?></span>
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($show['age_rating']); ?></span>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <a href="/pages/show_details.php?id=<?php echo $show['id']; ?>" class="btn btn-primary w-100">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    const genreFilter = document.getElementById('genreFilter');
    const showItems = document.querySelectorAll('.show-item');
    
    function filterShows() {
        const searchTerm = searchInput.value.trim().toLowerCase();
        const selectedGenre = genreFilter.value;
        
        showItems.forEach(show => {
            const title = show.querySelector('.card-title').textContent.toLowerCase();
            const genre = show.getAttribute('data-genre');
            
            const matchesSearch = searchTerm === '' || title.includes(searchTerm);
            const matchesGenre = selectedGenre === '' || genre === selectedGenre;
            
            if (matchesSearch && matchesGenre) {
                show.style.display = 'block';
            } else {
                show.style.display = 'none';
            }
        });
    }
    
    searchBtn.addEventListener('click', filterShows);
    searchInput.addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            filterShows();
        }
    });
    
    genreFilter.addEventListener('change', filterShows);
});
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?> 