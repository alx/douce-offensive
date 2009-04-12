class Main < Application
  
  before :ensure_authenticated
  
  def medias
    render :layout => "admin_media_rocket"
  end
  
end
