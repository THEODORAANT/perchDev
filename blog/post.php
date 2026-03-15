<?php include('../perch/runtime.php');?>
<?php
  $post_slug = perch_get('s');

?>
<!DOCTYPE html>
<html lang="en">
    <head>

    <style>

    .container {
      width: 80%;
      max-width: 1200px;
      margin: 0 auto;
    }



    .blog-post {
      background-color: #fff;
      margin-top: 20px;
      padding: 30px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .post-title {
      font-size: 2.5em;
      margin-bottom: 10px;
    }

    .post-meta {
      font-size: 1.1em;
      color: #666;
      margin-bottom: 20px;
    }

    .post-content p {
      line-height: 1.6;
      margin-bottom: 20px;
    }

    .post-image {
      width: 100%;
      height: auto;
      margin: 20px 0;
    }

    .post-footer {
      margin-top: 30px;
    }

    .social-link {
      margin-right: 15px;
      text-decoration: none;
      color: #007BFF;
      font-weight: bold;
    }

    .social-link:hover {
      text-decoration: underline;
    }
    </style>
</head>




<?php
            //perch_layout('global/header'); ?>
 <div class="container blog-post">
    <article class="post">

       <?php perch_blog_post($post_slug); ?>

    </article>
  </div>



<div class="container mt-5">



<!--
  <div class="row justify-content-center">
    <div class="col-lg-5 col-md-7 col-sm-4 text-left">
      <a class="aboutSection__button d-inline-block button button_fill urbanist-medium text-center"  href="/">Back to home</a>
    </div>
    <div class="col-lg-3 col-md-5 col-sm-2 text-right">
      <a class="aboutSection__button d-inline-block button button_fill urbanist-medium text-center" href="/blog">View more posts</a>
    </div>
  </div> -->

</div>



 <?php
  //perch_layout('global/'.$branch.'/footer');?>
