Merb.logger.info("Compiling routes...")
Merb::Router.prepare do
  
  authenticate do
    slice(:media_rocket, :path => 'medias')
  end
  
  slice(:merb_auth_slice_password, :name_prefix => nil, :path_prefix => "")
    
  match('/admin').to(:controller => 'main', :action =>'medias')
end