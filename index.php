<?php
session_start();
include("header.php"); // Include the Page Layout header
?>

<!-- Create a container -->
<div style='width:90%; margin:auto;'>
    <?php
    $imageDirectory = "Images/slideshow"; // Path to your slideshow images (relative)
    $imageFiles = glob($imageDirectory . "/*.{jpg,jpeg,png,gif}", GLOB_BRACE); // Array of all image file paths

    $imageMaxWidth = "400px"; // Set a default max-width, this can be changed as needed


      if(count($imageFiles)>0){
          ?>
             <!-- Carousel Structure -->
    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-indicators">
            <?php
            $first = true;
            $i = 0;
            foreach ($imageFiles as $image) {
                ?>
                <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="<?php echo $i; ?>"
                    class="<?php if ($first) { echo 'active'; $first = false; } ?>" aria-current="<?php if($i==0){ echo 'true';}?>" aria-label="Slide <?php echo $i+1; ?>"></button>
                <?php
              $i++;
            }
            ?>
          </div>
        <div class="carousel-inner">
            <?php
             $first = true;
            foreach ($imageFiles as $image) {
            ?>
                  <div class="carousel-item <?php if ($first) { echo 'active'; $first=false; } ?>">
                      <img src="<?php echo $image; ?>" class="d-block w-100" style="object-fit:contain; max-height:500px;" alt="Slideshow Image">
                  </div>
              <?php
           }
           ?>
        </div>
    </div>
     <?php
      } else {
          echo "<p>No images found for the slideshow</p>";
      }

    ?>
</div>
<?php
include("footer.php"); // Include the Page Layout footer
?>