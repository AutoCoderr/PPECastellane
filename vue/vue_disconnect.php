<body>
<script>
if (confirm("Voulez vous vous déconnecter ?")) {
	location.href = "/logout.php";
} else {
	location.href = "<?php echo $_SERVER["HTTP_REFERER"]; ?>";
}
</script>
</body>