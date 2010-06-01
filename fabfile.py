import os

from fabric.api import *

CONCRETE_VERSION = '5.4.0.5'
CONCRETE_URL = 'http://www.concrete5.org/download_file/-/view/15153/'
CONCRETE_TMP_FILE = '/tmp/concrete-%s.zip' % CONCRETE_VERSION

SSO_AUTH_LIB_VERSION = 'trunk'
SSO_AUTH_LIB_URL = 'http://svn.teknik.anime-kita.dk/selvbetjening/%s/integration/library/php/sso_api.inc.php' % SSO_AUTH_LIB_VERSION

def _apply(item_type, item):
	print('Applying %s' % item)
	local('cp -r %s/%s/* build/' % (item_type, item))	

def build():
	"""
	Build Concrete 5 Service with addons, patches, and configs.
	
	- Deploy concrete5
	- Deploy sso-auth-lib
	- Deploy addons
	- Apply patches
	- Add configuration
	"""
	
	print('Building Concrete 5 service')
	local('rm -rf build')
	local('mkdir build')
	
	print('Deploying concrete %s' % CONCRETE_VERSION)
	
	if os.path.exists(CONCRETE_TMP_FILE):
		print('Concrete %s download detected' % CONCRETE_VERSION)
	else:
		local('wget -O %s %s' % (CONCRETE_TMP_FILE, CONCRETE_URL), capture=False)
	
	local('unzip %s -d build/' % CONCRETE_TMP_FILE)
	
	# move concrete out of its base directory
	local('mv build/concrete%s/* build/' % CONCRETE_VERSION)
	local('rm -rf build/concrete%s/' % CONCRETE_VERSION)
	
	print('Deploying sso api library %s' % SSO_AUTH_LIB_VERSION)
	local('svn export %s build/libraries/sso_api.inc.php' % SSO_AUTH_LIB_URL)
	
	_apply('addons', 'kita-theme-addon')
	
	_apply('patches', 'kita-page-not-found-patch')
	_apply('patches', 'kita-sso-auth-patch')
	_apply('patches', 'slideshow-patch')
	
	_apply('configs', 'config-template')

def deploy():
	pass

def version():
	pass
