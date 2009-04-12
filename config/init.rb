# Go to http://wiki.merbivore.com/pages/init-rb
 
require 'config/dependencies.rb'
 
use_orm :datamapper
use_test :rspec
use_template_engine :erb
 
Merb::Config.use do |c|
  c[:use_mutex] = false
  c[:session_store] = 'cookie'  # can also be 'memory', 'memcache', 'container', 'datamapper
  
  # cookie session store configuration
  c[:session_secret_key]  = '0421c07aa34ce12194e15d8e5831a814ed625ef9'  # required for cookie session store
  c[:session_id_key] = '_douce_session_id' # cookie session id key, defaults to "_session_id"
  
  c[:login]     = "douceoffensive"
  c[:password]  = "tracks"
  
  c[:mediarocket_site]    = "douceoffensive"
end
 
Merb::BootLoader.before_app_loads do
  Merb::Plugins.config[:merb_slices] = { :queue => [:MerbAuthSlicePassword, 
                                                    :MediaRocket]}
                                                    
end
 
Merb::BootLoader.after_app_loads do
  
  #
  # Add default user if non-existent
  #
  begin
    if User.first.nil?
      u = User.new(:login => Merb::Config[:login])
      u.password = u.password_confirmation = Merb::Config[:password]
      u.save
    end
  rescue
  end
  
  #
  # Generate MediaRocket site if not existent
  # (should be place in MediaRocket Slice?)
  #
  begin
    if MediaRocket::Site
      site = MediaRocket::Site.first
      if site.nil?
        MediaRocket::Site.create :id => 1, :name => Merb::Config[:mediarocket_site]
      elsif site.name.nil?
        site.update_attributes :name => Merb::Config[:mediarocket_site]
      end
    end
  rescue
  end
  
end
