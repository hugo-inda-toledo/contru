<div class="frontEnd">
    <div class="container header">
        <div class="grid">
            <a class="brand">
            	<?= $this->Html->image('logo.png', ['alt' => 'AChEE', 'class' => 'logo imgResponse']); ?>
            </a>
            <ul class="main-menu horizontal-menu compact">
                <li>
                    <a class="active" href="<? __('#Link'); ?>"><?php echo ('Inicio'); ?></a>
                </li>
                <li>
                    <a href="<? __('#Link'); ?>"><?php echo ('Trámites'); ?></a>
                </li>
                <li>
                    <a href="<? __('#Link'); ?>"><?php echo ('Comunicación'); ?></a>
                </li>
                <li>
                    <a href="<? __('#Link'); ?>"><?php echo ('Utilidad'); ?></a>
                </li>
                <li>
                    <a href="<? __('#Link'); ?>"><?php echo ('Documentos'); ?></a>
                </li>
            </ul>
        </div>
    </div>

    <div class="container page-content">
        <div class="flex-grid">
            <div class="row">
            	<!-- primera columna -->
                <div id="column01" class="cell size6">
                	<!-- destacados -->
                    <div class="panel success">
                        <div class="heading">
                        	<?= $this->Html->image('white-iso.png', ['class' => 'iso']); ?>
                            <span class="title"><?= __('Destacados') ?></span>
                        </div>
                        <div class="content">
                            <?= $this->Html->image('tacataca.jpg', ['alt' => 'AChEE', 'class' => 'imgResponse']); ?>
                        </div>
                    </div>
                	<!-- noticias -->
                	<div class="notiHead padding20">
                		<div class="row cell-auto-size">
	                		<div class="cell">
	                			<h4>Notas de prensa</h4>
	                		</div>
	                		<div class="cell">
	                			<?= $this->Html->link(
	                				"Ver todas las noticias <span class='mif-chevron-right'></span>",
	                				"/users/home",
	                				['class' => 'seemore place-right', 'escape' => false]
                				); ?>
	                		</div>
                		</div>
                	</div>
                	<div class="notiBody padding20">
                		<div class="row cell-auto-size">
                			<div class="cell issue01">
                				<?= $this->Html->image('noti01.jpg', ['alt' => 'AChEE', 'class' => 'imgResponse']); ?>
                				<h5>Eficiencia Energética y Cambio Climático</h5>
                				<p>Sin duda el uso de energía en todas sus formas ha sido la causa principal del cambio climático, ya que la quema de combustibles.......</p>
                			</div>
                			<div class="cell issue02">
                				<?= $this->Html->image('noti02.jpg', ['alt' => 'AChEE', 'class' => 'imgResponse']); ?>
                				<h5>Se abren postulaciones para el cargo de "Inspector Técnico de Obras"</h5>
                				<p>El trabajo deberá ser desarrollado en la comuna de San Clemente. Se recibirán postulaciones hasta el 01 de julio de 2015........</p>
                			</div>
                		</div>
                	</div>
                </div>

            	<!-- segunda columna -->
                <div id="column02" class="cell size4">
                	<div class="row birthDay">
                		<div class="cell size4">
                			<div class="birthMonth">
                				<?= $this->Html->image('birth.png', ['class' => 'imgResponse']); ?>
                				<strong><?= __('Junio') ?></strong>
                				<span><?= __('Cumpleaños') ?></span>
                			</div>
                			<div class="birthYear">
                				<?= $date = date('m-d-', time()); ?><?= __('1983') ?>
                			</div>
                		</div>
                		<div class="cell size8">
                			<div class="birthProfile">
                				<?= $this->cell('Usuario::cumpleanero') ?>
                			</div>
                		</div>
                	</div>
                	<!-- <div class="image-container image-format-cycle" style="width: 100%;">
                		<div class="frame">
                			<div style="width: 100%; height: 166px; border-radius: 50%; background-image: url(https://metroui.org.ua/images/2.jpg); background-size: cover; background-repeat: no-repeat;"></div>
                		</div>
                	</div> -->
                </div>
            	<!-- tercera columna -->
                <div id="column03" class="cell size2">
                    <div class="tile">
                        <div class="tile-content iconic">
                            <span class="mif-earth" style="color: red;"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="flex-grid">
                <div class="row footBg">
                    <div class="cell size9">
                		<div class="row">
	                    	<div class="cell size1">
	                    		<div class="isoFoot">
		                        	<?= $this->Html->image('iso.png', ['alt' => 'AChEE', 'class' => 'imgResponse']); ?>
		                    	</div>
	                    	</div>
	                    	<div class="cell size11 descript">
		                        <small>Agencia Chilena de Eficiencia Energética (AChEE) Monseñor Nuncio Sótero Sanz n° 221. Providencia. Santiago - Chile.</small></br>
		                        <small>Email: <a href="mailto:info@acee.cl">info@acee.cl</a> / Teléfono: (56 2) 2571 2200</small>
	                    	</div>
	                    </div>
                    </div>
                    <div class="cell size3">
                    	<ul class="inline-list icon-list">
                    		<li>
                    			<?= $this->Html->link(
								    $this->Html->image("ico-fb.png", ["alt" => "Facebook"]),
								    "http://www.facebook.com",
								    ['escape' => false, 'target' => '_blank']
								); ?>
                    		</li>
                    		<li>
                    			<?= $this->Html->link(
								    $this->Html->image("ico-tt.png", ["alt" => "Facebook"]),
								    "http://www.twitter.com",
								    ['escape' => false, 'target' => '_blank']
								); ?>
                    		</li>
                    		<li>
                    			<?= $this->Html->link(
								    $this->Html->image("ico-yt.png", ["alt" => "Facebook"]),
								    "http://www.youtube.com",
								    ['escape' => false, 'target' => '_blank']
								); ?>
                    		</li>
                    	</ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>



<?= $this->cell('Indicators::santoral') ?>

<?= $this->cell('Indicators::economicos') ?>

<?= $this->cell('Indicators::cambio') ?>

<?= $this->cell('Indicators::indicadores') ?>

<?= $this->cell('Indicators::restriccion') ?>

<?= $this->cell('Indicators::clima') ?>

<?= $this->cell('Usuario::cumpleanero') ?>

<?= $this->cell('Usuario::proximos_cumpleanos') ?>


<?= $this->cell('Posts::avisos_clasificados') ?>