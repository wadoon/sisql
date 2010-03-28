function sprintf() {
 if( sprintf.arguments.length < 2 ) {
  return;
 }
 
 var data = sprintf.arguments[ 0 ];
 
 for( var k=1; k<sprintf.arguments.length; ++k ) {
 
  switch( typeof( sprintf.arguments[ k ] ) )
  {
   case 'string':
    data = data.replace( /%s/, sprintf.arguments[ k ] );
    break;
   case 'number':
    data = data.replace( /%d/, sprintf.arguments[ k ] );
    break;
   case 'boolean':
    data = data.replace( /%b/, sprintf.arguments[ k ] ? 'true' : 'false' );
    break;
   default:
    /// function | object | undefined
    break;
  }
 }
 return( data );
}
 
if( !String.sprintf ) {
 String.sprintf = sprintf;
}

///////////////////////////////////////////////////////////////////////////////
function find_data(name)
{
    for(var i = 0; i < templates.length; i++)
    {
       if(templates[i].name == name)
            return templates[i]; 
    }   
    return false;
}


function on_template_select() {
    var sel = $('#selTemplate');
    var val = sel.val();
   
    var data = find_data(val); 
    if(!data)
    {
        $('#nosel').show().html("Bitte treffen Sie eine Auswhl");
    }
    else
    {
        buildInput(data);
    }
}

function checkText(value)
{
   return value!="" && value; 
}

function checkNumber(value)
{
    return checkText(value) && !isNaN(value);
} 

function checkDate(value)
{
    return true;
}

function validate_update()
{
    $('.val-number, .val-text')
                        .removeClass("valid")
                        .removeClass("notvalid");

    $('.val-number').each(function(idx,item)
            {
                var value = $(item).val();            
                if( checkNumber(value) )
                    $(item).addClass("valid")
                else
                    $(item).addClass("notvalid")
            }
    );
    
    $('.val-text').each(function(idx,item)
            {
                var value = $(item).val();            
                if( checkText(value) )
                    $(item).addClass("valid")
                else
                    $(item).addClass("notvalid")
            }
    );
    update();
}

function update()
{
    var sql = find_data($('#selTemplate').val()).sql;

    $('.sqlinput').each(function(idx,item){
              var re = new RegExp("[$]"+$(item).attr("name"), "g");
              sql = sql.replace( re , $(item).val() );
            });
    $('#sqlpreview').html(sql);
    return sql;
}

function executeSql()
{
    var sql = update();
    $('div#loading').show();
    $('div#exec').empty();
    $('div#exec').load('rpc.php', {fn:'execsql',sql:sql}, function() 
        { $('div#loading').hide(); }
    );
}

function buildInput(data)
{
    var input =$('#input');
    input.empty();
    $("div#exec , #sqlpreview ").empty();
    var html = sprintf("<div class=\"description\">%s</div><table>",data['desc']);
    
    if(data.params.length>0)
    {
        for(var i = 0; i < data.params.length;i++)
        {
            field = data.params[i];
            html +=sprintf(
                  "<tr id=\"%s\"><td>%s:</td><td><input "+
                    " class=\"val-%s sqlinput\" type=\"text\" name=\"%s\"/></td>",
                  field.name, field.desc ,field.type,field.name);
        } 
        html += "</table>";
    } else {
        html +="<b>SQL-Statement besitzt keine Parameter</b>";
        validate_update();
    }
    input.html(html);
    $('.sqlinput').change(validate_update);
}


$(document).ready(function(){
    $('#selTemplate').change(on_template_select);
    $('#selTemplate').select(on_template_select);

    $('button#preview').click(validate_update);
    $('button#exec').click(executeSql);
});

on_template_select();
