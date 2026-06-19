<?php
function pintarRating($media, $total){
  $media = $media ? round($media, 1) : 0;
  $total = (int)$total;
  $redondeada = round($media);

  echo '<div class="activity-rating">';
  echo '<span class="stars">';

  for($i = 1; $i <= 5; $i++){
    echo $i <= $redondeada ? "★" : "☆";
  }

  echo '</span>';

  echo '<span class="rating-value">';
  if($total > 0){
    echo $media . '/5 · ' . $total . ' valoraciones';
  }else{
    echo 'Sin valoraciones';
  }
  echo '</span>';
  echo '</div>';
}
?>