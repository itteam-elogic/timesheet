</div>
    <!-- Javascripts-->
   <script language="javascript" type="text/javascript">
	$(function(){
		$('.sidebar-menu a').filter(function(){return this.href==location.href}).parent().addClass('active').siblings().removeClass('active')
		$('.sidebar-menu a').click(function(){
			$(this).parent().addClass('active').siblings().removeClass('active')	
		})
	})
	
   $('.count').each(function () { // Counter effects on employee etc...
		$(this).prop('Counter',0).animate({
			Counter: $(this).text()
		}, {
			duration: 4000,
			easing: 'swing',
			step: function (now) {
				$(this).text(Math.ceil(now));
			}
		});
   }); // Counter effects on employee etc...
	
	/*$(function(){
		$('.top-nav a').filter(function(){return this.href==location.href}).parent().addClass('active').siblings().removeClass('active')
		$('.top-nav a').click(function(){
			$(this).parent().addClass('active').siblings().removeClass('active')	
		})
	})	*/
	</script>
    <script src="<?php echo HTTP_JS_PATH; ?>essential-plugins.js"></script>
    <script src="<?php echo HTTP_JS_PATH; ?>bootstrap.min.js"></script>
    <script src="<?php echo HTTP_JS_PATH; ?>plugins/pace.min.js"></script>
    <script src="<?php echo HTTP_JS_PATH; ?>main.js"></script>
	<script type="text/javascript" src="<?php echo HTTP_JS_PATH; ?>plugins/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="<?php echo HTTP_JS_PATH; ?>plugins/dataTables.bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo HTTP_JS_PATH; ?>datatables_include_js/emp_reports.js"></script>
    <script type="text/javascript">$('#contactsTable').DataTable();</script>
	<script type="text/javascript">
		$('#organisationTable').DataTable({
			deferRender: true,
			pageLength: 25,
			order: []
		});		if ($('#qualityErrorLogTable').length) {
			$('#qualityErrorLogTable').DataTable({
				deferRender: true,
				processing: true,
				serverSide: true,
				ajax: {
					url: '<?php echo base_url('quality_error_log/ajax_list'); ?>',
					type: 'POST'
				},
				order: [[5, 'desc']],
				pageLength: 25,
				lengthMenu: [[25, 50, 100], [25, 50, 100]],
				columnDefs: [
					{ orderable: false, targets: [0, 13, 15] }
				],
				columns: [
					{ data: 0 },
					{ data: 1 },
					{ data: 2 },
					{ data: 3 },
					{ data: 4 },
					{ data: 5 },
					{ data: 6 },
					{ data: 7 },
					{ data: 8 },
					{ data: 9 },
					{ data: 10 },
					{ data: 11 },
					{ data: 12 },
					{ data: 13 },
					{ data: 14 },
					{ data: 15 }
				]
			});
		}	</script>
	</body>
</html>