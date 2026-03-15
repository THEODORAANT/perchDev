<?php include('../perch/runtime.php');?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <?php perch_layout('blog/meta'); ?>
    <?php perch_layout('blog/head'); ?>
</head>




<?php
            perch_layout('global/header'); ?>

<main class="page__content page__gap container mt-5">
		<div
				class="page__group_title d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
			<h1 class="page__title urbanist-medium m-0 flex-grow-1 lh-sm">
				<!--Men's Health Tests-->
			 <?php perch_content('Blog Header');?>
			</h1>

		</div>


    
    <?php
      perch_blog_custom(array(
          'filter' => 'postDateTime',
          'template' => 'post_in_list.html',
          'sort'       => 'postDateTime',
            'sort-order' => 'DESC',
          'count' => '4'
      ));
    ?>



 <?php
  perch_layout('global/'.$branch.'/footer');?>
