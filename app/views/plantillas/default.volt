{% extends "templates/default.volt" %}
{% block css %}
    {# Notifications #}
    {{ partial("partials/css_notifications_partial") }}
    {# Dialogs #}
    {{ stylesheet_link('library/dialog-effects/css/dialog-default.css') }}
    {# Select 2 #}
    {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
{% endblock %}
{% block js %}
    {# Notifications #}
    {{ partial("partials/js_notifications_partial") }}
    {# Dialogs #}
    {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
    {# Select 2 #}
    {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}
    <script>
        (function() {

            var dlgtrigger = document.querySelector( '[data-dialog]' ),
            somedialog = document.getElementById( dlgtrigger.getAttribute( 'data-dialog' ) ),
            dlg = new DialogFx( somedialog );

            dlgtrigger.addEventListener( 'click', dlg.toggle.bind(dlg) );

        })();
    </script> 
    
    <script type="text/javascript">
        $(function() {
            $(".select2").select2();
        });
    </script>
    
{% endblock %}    
{% block content %}
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <div class="clearfix"></div>
            <div class="space"></div>            
            
            <div class="title">
                Bienvenidos a sigma
            </div>    
            
            <hr class="basic-line" />
            
            <div class="subtitle">
                Bienvenidos a sigma
            </div>    
            
            <div class="small-text">
                Bienvenidos a sigma
            </div>    
            
            <p>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras fermentum nunc eu aliquet ullamcorper. In non nisi quis augue rhoncus maximus non nec leo. 
                Pellentesque vehicula est eget leo cursus, vel placerat urna efficitur. Mauris non nisi id lacus pretium vulputate vel eu purus. Sed eget pharetra nibh. 
                Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nunc ornare nulla nisi, vel aliquet diam ultrices id.
            </p>
            <p>
                Aenean ex leo, molestie consequat eleifend id, cursus tempor nunc. Nam consequat laoreet finibus. Aliquam ut semper lacus. Quisque iaculis massa at turpis suscipit 
                ultrices. Vestibulum in elit velit. Vivamus aliquam, eros in pellentesque ultrices, diam nunc consequat ligula, a mattis tortor enim at tortor. Pellentesque porttitor 
                neque molestie neque imperdiet dapibus. Sed vel condimentum dui, vel imperdiet elit. Vivamus ornare euismod risus, id euismod sapien facilisis quis. Donec arcu enim, 
                tincidunt nec massa nec, tempor vulputate metus. Ut auctor tincidunt metus vitae semper. Sed fermentum felis justo, sed pharetra dui ullamcorper ac. Aenean finibus
                arcu vel mi tempus, sit amet suscipit metus efficitur.
            </p>
            <p>
                Praesent vitae velit tortor. Nullam vitae turpis sed nulla commodo consequat. Morbi accumsan imperdiet metus id blandit. Aenean mattis maximus pulvinar. 
                Suspendisse potenti. Pellentesque id sodales velit, elementum efficitur nibh. Sed sem nisl, condimentum et volutpat ut, accumsan nec tortor. Quisque mattis 
                lectus lacinia, mattis ligula vel, mattis eros.
                
            </p>
            
            <hr class="basic-line" />
            
            <a href="{{url('')}}">Esto es un enlace</a>
    
            
            <hr class="basic-line" />
            
            <table class="table table-bordered">
                <thead class="theader">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Mobile</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class="user-name">gary coleman</td><td class="user-email">gary.coleman21@example.com</td><td class="user-phone">(398)-332-5385</td><td class="user-mobile">(888)-677-3719</td></tr>
                    <tr><td class="user-name">rose parker</td><td class="user-email">rose.parker16@example.com</td><td class="user-phone">(293)-873-2247</td><td class="user-mobile">(216)-889-4933</td></tr>
                    <tr><td class="user-name">chloe nelson</td><td class="user-email">chloe.nelson18@example.com</td><td class="user-phone">(957)-213-3499</td><td class="user-mobile">(207)-516-4474</td></tr>
                    <tr><td class="user-name">eric bell</td><td class="user-email">eric.bell16@example.com</td><td class="user-phone">(897)-762-9782</td><td class="user-mobile">(565)-627-3002</td></tr>
                    <tr><td class="user-name">douglas hayes</td><td class="user-email">douglas.hayes92@example.com</td><td class="user-phone">(231)-391-6269</td><td class="user-mobile">(790)-838-2130</td></tr>
                    <tr><td class="user-name">cameron brown</td><td class="user-email">cameron.brown32@example.com</td><td class="user-phone">(204)-488-5204</td><td class="user-mobile">(508)-463-6811</td></tr>
                    <tr><td class="user-name">nevaeh diaz</td><td class="user-email">nevaeh.diaz99@example.com</td><td class="user-phone">(436)-578-2946</td><td class="user-mobile">(906)-412-3302</td></tr>
                    <tr><td class="user-name">kathy miller</td><td class="user-email">kathy.miller62@example.com</td><td class="user-phone">(724)-705-3555</td><td class="user-mobile">(764)-841-2531</td></tr>
                    <tr><td class="user-name">susan king</td><td class="user-email">susan.king88@example.com</td><td class="user-phone">(774)-205-7754</td><td class="user-mobile">(639)-267-9728</td></tr>
                    <tr><td class="user-name">jeffery ramirez</td><td class="user-email">jeffery.ramirez83@example.com</td><td class="user-phone">(723)-243-7706</td><td class="user-mobile">(172)-597-3422</td></tr>
                    <tr><td class="user-name">gary coleman</td><td class="user-email">gary.coleman21@example.com</td><td class="user-phone">(398)-332-5385</td><td class="user-mobile">(888)-677-3719</td></tr>
                    <tr><td class="user-name">rose parker</td><td class="user-email">rose.parker16@example.com</td><td class="user-phone">(293)-873-2247</td><td class="user-mobile">(216)-889-4933</td></tr>
                    <tr><td class="user-name">chloe nelson</td><td class="user-email">chloe.nelson18@example.com</td><td class="user-phone">(957)-213-3499</td><td class="user-mobile">(207)-516-4474</td></tr>
                    <tr><td class="user-name">eric bell</td><td class="user-email">eric.bell16@example.com</td><td class="user-phone">(897)-762-9782</td><td class="user-mobile">(565)-627-3002</td></tr>
                    <tr><td class="user-name">douglas hayes</td><td class="user-email">douglas.hayes92@example.com</td><td class="user-phone">(231)-391-6269</td><td class="user-mobile">(790)-838-2130</td></tr>
                    <tr><td class="user-name">cameron brown</td><td class="user-email">cameron.brown32@example.com</td><td class="user-phone">(204)-488-5204</td><td class="user-mobile">(508)-463-6811</td></tr>
                    <tr><td class="user-name">nevaeh diaz</td><td class="user-email">nevaeh.diaz99@example.com</td><td class="user-phone">(436)-578-2946</td><td class="user-mobile">(906)-412-3302</td></tr>
                    <tr><td class="user-name">kathy miller</td><td class="user-email">kathy.miller62@example.com</td><td class="user-phone">(724)-705-3555</td><td class="user-mobile">(764)-841-2531</td></tr>
                    <tr><td class="user-name">susan king</td><td class="user-email">susan.king88@example.com</td><td class="user-phone">(774)-205-7754</td><td class="user-mobile">(639)-267-9728</td></tr>
                    <tr><td class="user-name">jeffery ramirez</td><td class="user-email">jeffery.ramirez83@example.com</td><td class="user-phone">(723)-243-7706</td><td class="user-mobile">(172)-597-3422</td></tr>
                    <tr><td class="user-name">gary coleman</td><td class="user-email">gary.coleman21@example.com</td><td class="user-phone">(398)-332-5385</td><td class="user-mobile">(888)-677-3719</td></tr>
                    <tr><td class="user-name">rose parker</td><td class="user-email">rose.parker16@example.com</td><td class="user-phone">(293)-873-2247</td><td class="user-mobile">(216)-889-4933</td></tr>
                    <tr><td class="user-name">chloe nelson</td><td class="user-email">chloe.nelson18@example.com</td><td class="user-phone">(957)-213-3499</td><td class="user-mobile">(207)-516-4474</td></tr>
                    <tr><td class="user-name">eric bell</td><td class="user-email">eric.bell16@example.com</td><td class="user-phone">(897)-762-9782</td><td class="user-mobile">(565)-627-3002</td></tr>
                    <tr><td class="user-name">douglas hayes</td><td class="user-email">douglas.hayes92@example.com</td><td class="user-phone">(231)-391-6269</td><td class="user-mobile">(790)-838-2130</td></tr>
                    <tr><td class="user-name">cameron brown</td><td class="user-email">cameron.brown32@example.com</td><td class="user-phone">(204)-488-5204</td><td class="user-mobile">(508)-463-6811</td></tr>
                    <tr><td class="user-name">nevaeh diaz</td><td class="user-email">nevaeh.diaz99@example.com</td><td class="user-phone">(436)-578-2946</td><td class="user-mobile">(906)-412-3302</td></tr>
                    <tr><td class="user-name">kathy miller</td><td class="user-email">kathy.miller62@example.com</td><td class="user-phone">(724)-705-3555</td><td class="user-mobile">(764)-841-2531</td></tr>
                    <tr><td class="user-name">susan king</td><td class="user-email">susan.king88@example.com</td><td class="user-phone">(774)-205-7754</td><td class="user-mobile">(639)-267-9728</td></tr>
                    <tr><td class="user-name">jeffery ramirez</td><td class="user-email">jeffery.ramirez83@example.com</td><td class="user-phone">(723)-243-7706</td><td class="user-mobile">(172)-597-3422</td></tr>
                    <tr><td class="user-name">gary coleman</td><td class="user-email">gary.coleman21@example.com</td><td class="user-phone">(398)-332-5385</td><td class="user-mobile">(888)-677-3719</td></tr>
                    <tr><td class="user-name">rose parker</td><td class="user-email">rose.parker16@example.com</td><td class="user-phone">(293)-873-2247</td><td class="user-mobile">(216)-889-4933</td></tr>
                    <tr><td class="user-name">chloe nelson</td><td class="user-email">chloe.nelson18@example.com</td><td class="user-phone">(957)-213-3499</td><td class="user-mobile">(207)-516-4474</td></tr>
                    <tr><td class="user-name">eric bell</td><td class="user-email">eric.bell16@example.com</td><td class="user-phone">(897)-762-9782</td><td class="user-mobile">(565)-627-3002</td></tr>
                    <tr><td class="user-name">douglas hayes</td><td class="user-email">douglas.hayes92@example.com</td><td class="user-phone">(231)-391-6269</td><td class="user-mobile">(790)-838-2130</td></tr>
                    <tr><td class="user-name">cameron brown</td><td class="user-email">cameron.brown32@example.com</td><td class="user-phone">(204)-488-5204</td><td class="user-mobile">(508)-463-6811</td></tr>
                    <tr><td class="user-name">nevaeh diaz</td><td class="user-email">nevaeh.diaz99@example.com</td><td class="user-phone">(436)-578-2946</td><td class="user-mobile">(906)-412-3302</td></tr>
                </tbody>
            </table>
        </div>    
    </div>
            
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <h1>Buttons</h1>
            <h3>Fill style</h3>
            <hr />
        </div>
    </div>    
     
    <div class="row">
        <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 wrap">        
            <h3>Extra small</h3>
            <button class="button fill btn btn-xs primary">Primary</button>
            <button class="button fill btn btn-xs default">Default</button>
            <button class="button fill btn btn-xs success">Success</button>
            <button class="button fill btn btn-xs info">Info</button>
            <button class="button fill btn btn-xs warning">Warning</button>
            <button class="button fill btn btn-xs danger">Danger</button>
        </div>       
        <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 wrap">
            <h3>Small</h3>
            <button class="button fill btn btn-sm primary">Primary</button>
            <button class="button fill btn btn-sm default">Default</button>
            <button class="button fill btn btn-sm success">Success</button>
            <button class="button fill btn btn-sm info">Info</button>
            <button class="button fill btn btn-sm warning">Warning</button>
            <button class="button fill btn btn-sm danger">Danger</button>
        </div>
        <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 wrap">
            <h3>Medium</h3>
            <button class="button fill btn btn-md primary">Primary</button>
            <button class="button fill btn btn-md default">Default</button>
            <button class="button fill btn btn-md success">Success</button>
            <button class="button fill btn btn-md info">Info</button>
            <button class="button fill btn btn-md warning">Warning</button>
            <button class="button fill btn btn-md danger">Danger</button>
        </div>
        <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 wrap"> 
            <h3>Large</h3>
            <button class="button fill btn btn-lg primary">Primary</button>
            <button class="button fill btn btn-lg default">Default</button>
            <button class="button fill btn btn-lg success">Success</button>
            <button class="button fill btn btn-lg info">Info</button>
            <button class="button fill btn btn-lg warning">Warning</button>
            <button class="button fill btn btn-lg danger">Danger</button>
        </div>
    </div>
            
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <hr class="basic-line" />
            <h3>Round style</h3>
            <h5>Extra Small</h5>
            <button class="button btn btn-xs-round primary-inverted">
                <span class="glyphicon glyphicon-music"></span>
            </button>
            <button class="button btn btn-xs-round default-inverted">
                <span class="glyphicon glyphicon-music"></span>
            </button>
            <button class="button btn btn-xs-round success-inverted">
                <span class="glyphicon glyphicon-music"></span>
            </button>
            <button class="button btn btn-xs-round info-inverted">
                <span class="glyphicon glyphicon-music"></span>
            </button>
            <button class="button btn btn-xs-round warning-inverted">
                <span class="glyphicon glyphicon-music"></span>
            </button>
            <button class="button btn btn-xs-round danger-inverted">
                <span class="glyphicon glyphicon-music"></span>
            </button>
        </div>    
    </div>   
    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <h5>Small</h5>
            <button class="button btn btn-sm-round primary-inverted">
                <span class="glyphicon glyphicon-music"></span>
            </button>
            <button class="button btn btn-sm-round default-inverted">
                <span class="glyphicon glyphicon-music"></span>
            </button>
            <button class="button btn btn-sm-round success-inverted">
                <span class="glyphicon glyphicon-music"></span>
            </button>
            <button class="button btn btn-sm-round info-inverted">
                <span class="glyphicon glyphicon-music"></span>
            </button>
            <button class="button btn btn-sm-round warning-inverted">
                <span class="glyphicon glyphicon-music"></span>
            </button>
            <button class="button btn btn-sm-round danger-inverted">
                <span class="glyphicon glyphicon-music"></span>
            </button>
        </div>
    </div>
            
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">        
            <h5>Medium</h5>
            <button class="button btn btn-md-round primary-inverted">
                <span class="glyphicon glyphicon-music"></span>
            </button>
            <button class="button btn btn-md-round default-inverted">
                <span class="glyphicon glyphicon-music"></span>
            </button>
            <button class="button btn btn-md-round success-inverted">
                <span class="glyphicon glyphicon-music"></span>
            </button>
            <button class="button btn btn-md-round info-inverted">
                <span class="glyphicon glyphicon-music"></span>
            </button>
            <button class="button btn btn-md-round warning-inverted">
                <span class="glyphicon glyphicon-music"></span>
            </button>
            <button class="button btn btn-md-round danger-inverted">
                <span class="glyphicon glyphicon-music"></span>
            </button>
        </div>
    </div>
            
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">               
            <h5>Large</h5>
            <button class="button btn btn-lg-round primary-inverted">
                <span class="glyphicon glyphicon-music"></span>
            </button>
            <button class="button btn btn-lg-round default-inverted">
                <span class="glyphicon glyphicon-music"></span>
            </button>
            <button class="button btn btn-lg-round success-inverted">
                <span class="glyphicon glyphicon-music"></span>
            </button>
            <button class="button btn btn-lg-round info-inverted">
                <span class="glyphicon glyphicon-music"></span>
            </button>
            <button class="button btn btn-lg-round warning-inverted">
                <span class="glyphicon glyphicon-music"></span>
            </button>
            <button class="button btn btn-lg-round danger-inverted">
                <span class="glyphicon glyphicon-music"></span>
            </button>
        </div>
    </div>
            
    <div class="row">
       <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap"> 
            <h1>Buttons</h1>
            <h3>Simple style</h3>
            <button class="button btn btn-xs primary-inverted">Primary</button>
            <button class="button btn btn-sm default-inverted">Default</button>
            <button class="button btn btn-md success-inverted">Success</button>
            <button class="button btn btn-lg info-inverted">Info</button>
            <button class="button btn btn-sm warning-inverted">Warning</button>
            <button class="button btn btn-sm danger-inverted">Danger</button>
        </div>
    </div>
            
            
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 wrap">        
            <div class="fill-block fill-block-primary" >
                <div class="header">
                    Header
                </div>
                <div class="body">
                    This is my fucking content honey!
                </div>
                <div class="footer">
                    Uiiiiiiiiiiiiiigh!
                </div>
            </div>     
        </div>
        
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 wrap">        
            <div class="fill-block fill-block-basic" >
                <div class="header">
                    Header
                </div>
                <div class="body">
                    This is my fucking content honey!
                </div>
                <div class="footer">
                    Uiiiiiiiiiiiiiigh!
                </div>
            </div>     
        </div>
        
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 wrap">        
            <div class="fill-block fill-block-default" >
                <div class="header">
                    Header
                </div>
                <div class="body">
                    This is my fucking content honey!
                </div>
                <div class="footer">
                    Uiiiiiiiiiiiiiigh!
                </div>
            </div>     
        </div>
        
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 wrap">        
            <div class="fill-block fill-block-success" >
                <div class="header">
                    Header
                </div>
                <div class="body">
                    This is my fucking content honey!
                </div>
                <div class="footer">
                    Uiiiiiiiiiiiiiigh!
                </div>
            </div>     
        </div>
        
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 wrap">        
            <div class="fill-block fill-block-info" >
                <div class="header">
                    Header
                </div>
                <div class="body">
                    This is my fucking content honey!
                </div>
                <div class="footer">
                    Uiiiiiiiiiiiiiigh!
                </div>
            </div>     
        </div>
        
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 wrap">        
            <div class="fill-block fill-block-danger" >
                <div class="header">
                    Header
                </div>
                <div class="body">
                    This is my fucking content honey!
                </div>
                <div class="footer">
                    Uiiiiiiiiiiiiiigh!
                </div>
            </div>     
        </div>
        
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 wrap">        
            <div class="fill-block fill-block-warning" >
                <div class="header">
                    Header
                </div>
                <div class="body">
                    This is my fucking content honey!
                </div>
                <div class="footer">
                    Uiiiiiiiiiiiiiigh!
                </div>
            </div>     
        </div>
    </div>
            
    <div class="row">
       <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap"> 
           <div class="block block-basic">
               <div class="header">Header</div>
               <div class="body">
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras fermentum nunc eu aliquet ullamcorper. In non nisi quis augue rhoncus maximus non nec leo. 
                        Pellentesque vehicula est eget leo cursus, vel placerat urna efficitur. Mauris non nisi id lacus pretium vulputate vel eu purus. Sed eget pharetra nibh. 
                        Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nunc ornare nulla nisi, vel aliquet diam ultrices id.</div>
                    </p>
               <div class="footer">Footer</div>
           </div>    
       </div>       
    </div>      
        
    <div class="row">
       <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap"> 
           <div class="block block-default">
               <div class="header">Header</div>
               <div class="body">
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras fermentum nunc eu aliquet ullamcorper. In non nisi quis augue rhoncus maximus non nec leo. 
                        Pellentesque vehicula est eget leo cursus, vel placerat urna efficitur. Mauris non nisi id lacus pretium vulputate vel eu purus. Sed eget pharetra nibh. 
                        Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nunc ornare nulla nisi, vel aliquet diam ultrices id.</div>
                    </p>
               <div class="footer">Footer</div>
           </div>    
       </div>       
    </div>       
        
    <div class="row">
       <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap"> 
           <div class="block block-primary">
               <div class="header">Header</div>
               <div class="body">
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras fermentum nunc eu aliquet ullamcorper. In non nisi quis augue rhoncus maximus non nec leo. 
                        Pellentesque vehicula est eget leo cursus, vel placerat urna efficitur. Mauris non nisi id lacus pretium vulputate vel eu purus. Sed eget pharetra nibh. 
                        Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nunc ornare nulla nisi, vel aliquet diam ultrices id.</div>
                    </p>
               <div class="footer">Footer</div>
           </div>    
       </div>       
    </div>   
        
    <div class="row">
       <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap"> 
           <div class="block block-success">
               <div class="header">Header</div>
               <div class="body">
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras fermentum nunc eu aliquet ullamcorper. In non nisi quis augue rhoncus maximus non nec leo. 
                        Pellentesque vehicula est eget leo cursus, vel placerat urna efficitur. Mauris non nisi id lacus pretium vulputate vel eu purus. Sed eget pharetra nibh. 
                        Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nunc ornare nulla nisi, vel aliquet diam ultrices id.</div>
                    </p>
               <div class="footer">Footer</div>
           </div>    
       </div>       
    </div>     
        
    <div class="row">
       <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap"> 
           <div class="block block-warning">
               <div class="header">Header</div>
               <div class="body">
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras fermentum nunc eu aliquet ullamcorper. In non nisi quis augue rhoncus maximus non nec leo. 
                        Pellentesque vehicula est eget leo cursus, vel placerat urna efficitur. Mauris non nisi id lacus pretium vulputate vel eu purus. Sed eget pharetra nibh. 
                        Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nunc ornare nulla nisi, vel aliquet diam ultrices id.</div>
                    </p>
               <div class="footer">Footer</div>
           </div>    
       </div>       
    </div>      
        
    <div class="row">
       <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap"> 
           <div class="block block-danger">
               <div class="header">Header</div>
               <div class="body">
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras fermentum nunc eu aliquet ullamcorper. In non nisi quis augue rhoncus maximus non nec leo. 
                        Pellentesque vehicula est eget leo cursus, vel placerat urna efficitur. Mauris non nisi id lacus pretium vulputate vel eu purus. Sed eget pharetra nibh. 
                        Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nunc ornare nulla nisi, vel aliquet diam ultrices id.</div>
                    </p>
               <div class="footer">Footer</div>
           </div>    
       </div>       
    </div>     
        
    <div class="row">
       <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap"> 
           <div class="block block-info">
               <div class="header">Header</div>
               <div class="body">
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras fermentum nunc eu aliquet ullamcorper. In non nisi quis augue rhoncus maximus non nec leo. 
                        Pellentesque vehicula est eget leo cursus, vel placerat urna efficitur. Mauris non nisi id lacus pretium vulputate vel eu purus. Sed eget pharetra nibh. 
                        Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nunc ornare nulla nisi, vel aliquet diam ultrices id.</div>
                    </p>
               <div class="footer">Footer</div>
           </div>    
       </div>       
    </div>      
            
    <div class="row">
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 wrap"> 
            <h1>Cajas</h1>
            <div class="box box-primary">
                <div class="icon">
                    <div class="inner">
                        <span class="glyphicon glyphicon-apple"></span>
                    </div>
                </div>    
                <div class="content">
                    <div class="inner">
                        <span class="number">
                            1200
                        </span>    
                        <span class="text">
                            Indicator
                        </span>
                    </div>    
                </div>    
            </div>    
        </div>
        
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 wrap">
            <div class="box box-danger">
                <div class="icon">
                    <div class="inner">
                        <span class="glyphicon glyphicon-apple"></span>
                    </div>
                </div>    
                <div class="content">
                    <div class="inner">
                        <span class="number">
                            1200
                        </span>    
                        <span class="text">
                            Indicator
                        </span>
                    </div>    
                </div>    
            </div>    
        </div>
        
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 wrap">
            <div class="box box-info">
                <div class="icon">
                    <div class="inner">
                        <span class="glyphicon glyphicon-apple"></span>
                    </div>
                </div>    
                <div class="content">
                    <div class="inner">
                        <span class="number">
                            1200
                        </span>    
                        <span class="text">
                            Indicator
                        </span>
                    </div>    
                </div>    
            </div>    
        </div>
        
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 wrap">
            <div class="box box-warning">
                <div class="icon">
                    <div class="inner">
                        <span class="glyphicon glyphicon-apple"></span>
                    </div>
                </div>    
                <div class="content">
                    <div class="inner">
                        <span class="number">
                            1200
                        </span>    
                        <span class="text">
                            Indicator
                        </span>
                    </div>    
                </div>    
            </div>    
        </div>
        
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 wrap">
            <div class="box box-success">
                <div class="icon">
                    <div class="inner">
                        <span class="glyphicon glyphicon-apple"></span>
                    </div>
                </div>    
                <div class="content">
                    <div class="inner">
                        <span class="number">
                            1200
                        </span>    
                        <span class="text">
                            Indicator
                        </span>
                    </div>    
                </div>    
            </div>    
        </div>
        
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 wrap">
            <div class="box box-default">
                <div class="icon">
                    <div class="inner">
                        <span class="glyphicon glyphicon-phone-alt"></span>
                    </div>
                </div>    
                <div class="content">
                    <div class="inner">
                        <span class="number">
                            1200
                        </span>    
                        <span class="text">
                            Indicator
                        </span>
                    </div>    
                </div>    
            </div>    
        </div>
    </div>    
            
    <hr class="basic-line" />    
        
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <h1>Notificaciones</h1>
            <h3>Slide on top</h3>
            <div class="row">
                <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3">
                    <button class="button btn btn-sm primary-inverted" onClick="slideOnTop('This is a primary message', 4000, 'glyphicon glyphicon-home', 'primary');">Primary</button>
                    <button class="button btn btn-sm default-inverted" onClick="slideOnTop('This is a default message', 4000, 'glyphicon glyphicon-cog', 'default');">Default</button>
                    <button class="button btn btn-sm success-inverted" onClick="slideOnTop('This is a success message', 4000, 'glyphicon glyphicon-ok', 'success');">Success</button>
                    <button class="button btn btn-sm info-inverted" onClick="slideOnTop('This is a info message', 4000, 'glyphicon glyphicon-info-sign', 'info');">Info</button>
                    <button class="button btn btn-sm warning-inverted" onClick="slideOnTop('This is a warning message', 4000, 'glyphicon glyphicon-warning-sign', 'warning');">Warning</button>
                    <button class="button btn btn-sm danger-inverted" onClick="slideOnTop('This is a danger message', 4000, 'glyphicon glyphicon-remove-sign', 'danger');">Danger</button>
                </div>
            </div>
            
            <hr />
            
            <h3>Bouncey Flip</h3>
            <div class="row">
                <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3">
                    <button class="button btn btn-sm primary-inverted" onClick="bouncyFlip('This is a primary message', 80000000, 'glyphicon glyphicon-home', 'primary');">Primary</button>
                    <button class="button btn btn-sm default-inverted" onClick="bouncyFlip('This is a default message', 4000, 'glyphicon glyphicon-cog', 'default');">Default</button>
                    <button class="button btn btn-sm success-inverted" onClick="bouncyFlip('This is a success message', 4000, 'glyphicon glyphicon-ok', 'success');">Success</button>
                    <button class="button btn btn-sm info-inverted" onClick="bouncyFlip('This is a info message', 4000, 'glyphicon glyphicon-info-sign', 'info');">Info</button>
                    <button class="button btn btn-sm warning-inverted" onClick="bouncyFlip('This is a warning message', 4000, 'glyphicon glyphicon-warning-sign', 'warning');">Warning</button>
                    <button class="button btn btn-sm danger-inverted" onClick="bouncyFlip('This is a danger message', 4000, 'glyphicon glyphicon-remove-sign', 'danger');">Danger</button>
                </div>
            </div>
            <hr />
            
            <h3>Box Spinner</h3>
            <div class="row">
                <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3">
                    <button class="button btn btn-sm primary-inverted" onClick="boxSpinner('This is a primary message', 4000, 'primary');">Primary</button>
                    <button class="button btn btn-sm default-inverted" onClick="boxSpinner('This is a default message', 4000, 'default');">Default</button>
                    <button class="button btn btn-sm success-inverted" onClick="boxSpinner('This is a success message', 4000, 'success');">Success</button>
                    <button class="button btn btn-sm info-inverted" onClick="boxSpinner('This is a info message', 4000, 'info');">Info</button>
                    <button class="button btn btn-sm warning-inverted" onClick="boxSpinner('This is a warning message', 4000, 'warning');">Warning</button>
                    <button class="button btn btn-sm danger-inverted" onClick="boxSpinner('This is a danger message', 4000,'danger');">Danger</button>
                </div>
            </div>
        </div>    
    </div>    
            
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <h1>Inputs</h1>
            <hr />
            
            <form class="form-horizontal">
                <div class="form-group">
                    <div class="col-sm-8 col-md-offset-2">
                        <div class="form-group">
                            <input class="undeline-input" type="email" placeholder="Email" autofocus/>
                        </div>
                        
                        <div class="form-group">
                            <input class="input-danger undeline-input" type="text" placeholder="Nombre" autofocus/>
                        </div>
                        
                        <div class="form-group">
                            <input class="input-info undeline-input" type="password" placeholder="Password"/>
                        </div>
                        
                        <div class="form-group">
                            <input class="input-warning undeline-input" type="date" placeholder="Fecha"/>
                        </div>
                        
                        <div class="form-group">
                            <input class="input-default undeline-input" type="date" placeholder="Disable" disabled/>
                        </div>
                        
                        <div class="form-group">
                            <input class="input-default undeline-input" type="text" placeholder="Required" required/>
                        </div>
                        
                        <div class="form-group">
                            <input class="input-success undeline-input" type="text" placeholder="Success"/>
                        </div>
                        
                        <div class="form-group">
                            <select class="select2" >
                                <option value="1">First</option>
                                <option value="2">Second</option>
                                <option value="3">Third</option>
                            </select>
                        </div>    
                    </div>
                </div>
            </form>
        </div>
    </div>
       
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <h1>Dialogs</h1>
            
            <button data-dialog="somedialog" class="trigger">Open Dialog</button>
            
            <div id="somedialog" class="dialog">
                <div class="dialog__overlay"></div>
                <div class="dialog__content">
                    <div class="morph-shape">
                        <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
                            <rect x="3" y="3" fill="none" width="556" height="276"/>
                        </svg>
                    </div>
                    <div class="dialog-inner">
                        <h2><strong>Howdy</strong>, I'm a dialog box</h2>
                        <div><button class="action" data-dialog-close>Close</button></div>
                    </div>
                </div>
            </div>
        </div>
    </div>   
    
    <br><br><br>
    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <div class="title">
                Basic form
            </div>
            <p>
              This is an area's explanation
            </p>
        </div>
    </div>   
    
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
        <form class="">
          <div class="block block-success">
            <div class="body form-horizontal">
              <br>
              <div class="form-group">
                <label for="email" class="col-sm-2 control-label">*Email</label>
                <div class="col-sm-10">
                  <input class="undeline-input form-control" id='email' type="email" placeholder="Email" autofocus/>
                </div>
              </div>

              <div class="form-group">
                <label for="password" class="col-sm-2 control-label">Password</label>
                <div class="col-sm-10">
                  <input type="password" class="undeline-input form-control" id="password" placeholder="Password">
                </div>
              </div>

              <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <div class="checkbox">
                    <label>
                      <input type="checkbox"> Remember me
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <div class="footer text-right">
              <button class="button btn btn-xs-round danger-inverted">
                  <span class="glyphicon glyphicon-remove"></span>
              </button>
              <button class="button btn btn-xs-round success-inverted">
                  <span class="glyphicon glyphicon-ok"></span>
              </button>
            </div>
          </div> 
        </form>
      </div>
      
      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
        <div class="fill-block fill-block-primary">
            <div class="header">
                Instructions
            </div>
            <div class="body">
              <p>
                Please, before starts, to read this recomendations
              </p>
              <ul>
                <li>The fields with * are mandatory</li>
                <li>The email field, must be 300 length</li>
              </ul>
            </div>
        </div>
      </div>
    </div>
{% endblock %}