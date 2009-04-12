Merb.logger.info("Compiling routes...")
Merb::Router.prepare do
  
  authenticate do
    slice(:webbastic, :path => 'cms')
    slice(:media_rocket, :path => 'library')
  end
  
  slice(:merb_auth_slice_password, :name_prefix => nil, :path_prefix => "")
    
  match('/admin').to(:controller => 'main', :action =>'admin')
  match('/admin/medias').to(:controller => 'main', :action =>'medias')
end