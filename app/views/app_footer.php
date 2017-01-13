<?php if (AppUser::$signed_in) { ?>
			</section>
		</div>

		<footer class="main-footer">
			&copy;<?php echo date('Y', time()) . ' ' . Config::$value['title']; ?>
		</footer>
	</div>
<?php } else { ?>
		</div>
	</div>
<?php } ?>

<!-- JS -->
<script src="<?php echo $this->url; ?>/public/js/jquery-3.1.1.min.js"></script>
<script src="<?php echo $this->url; ?>/public/js/bootstrap.min.js"></script>
<script src="<?php echo $this->url; ?>/public/js/app.min.js"></script>
<script src="<?php echo $this->url; ?>/public/js/datatables.min.js"></script>
<script src="<?php echo $this->url; ?>/public/js/custom.js?version=<?php echo VERSION; ?>"></script>

</body>
</html>