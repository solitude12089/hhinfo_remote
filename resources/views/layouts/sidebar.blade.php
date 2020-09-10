<div class="navbar-default sidebar {{ isset($menu_open)&&$menu_open ? "" : "off" }}" role="navigation">
	<div class="sidebar-nav navbar-collapse">
		<ul class="nav" id="side-menu">
			
			@if(Auth::user()->role==9)

				<li>
					<a href="#">
						<i class="fa fa-cogs fa-fw"></i>管理員功能
						<span class="fa arrow"></span>
					</a>
					<ul class="nav nav-second-level">
						<li>
							<a href="{{ url ('/admin/account') }}">
								<i class="fa fa-user fa-fw"></i>帳號管理
							</a>
							<a href="{{ url ('/admin/group') }}">
								<i class="fa fa-map-marker fa-fw"></i>區域管理
							</a>
							<a href="{{ url ('/admin/device') }}">
								<i class="fa fa-desktop fa-fw"></i>設備管理
							</a>

						</li>
					</ul>

				</li>

			@endif

			<li>
					<a href="#">
						<i class="fa fa-user fa-fw"></i>客戶管理
						<span class="fa arrow"></span>
					</a>
					<ul class="nav nav-second-level">
						<li>
							<a href="{{ url ('/customer/index') }}">
								<i class="fa fa-user fa-fw"></i>客戶列表
							</a>

							<a href="{{ url ('/customer/spcard') }}">
								<i class="fa fa-credit-card fa-fw"></i>全區卡設定
							</a>
						

						</li>
					</ul>

			</li>

			<li>
					<a href="#">
						<i class="fa fa-desktop fa-fw"></i>設備管理
						<span class="fa arrow"></span>
					</a>
					<ul class="nav nav-second-level">
						<li>
							<a href="{{ url ('/remote/index') }}">
								<i class="fa fa-bolt fa-fw"></i>遠端操作
							</a>
						

						</li>
					</ul>

			</li>

			<li>
					<a href="#">
						<i class="fa  fa-clock-o fa-fw"></i>租借管理
						<span class="fa arrow"></span>
					</a>
					<ul class="nav nav-second-level">
						<li>
							<a href="{{ url ('/booking/index') }}">
								<i class="fa fa-clock-o fa-fw"></i>預約租借
							</a>

							<a href="{{ url ('/booking/query') }}">
								<i class="fa fa-search fa-fw"></i>租借查詢
							</a>

							<a href="{{ url ('/booking/calendar') }}">
								<i class="fa fa-calendar fa-fw"></i>行事曆
							</a>
						

						</li>
					</ul>

			</li>

		</ul>
	</div>
</div>