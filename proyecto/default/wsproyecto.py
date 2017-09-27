import logging
logging.basicConfig(level=logging.DEBUG)
from spyne import ComplexModel, Unicode, Integer, Array, TTableModel, rpc
from spyne.application import Application
from spyne.decorator import srpc
from spyne.service import ServiceBase
from spyne.model.primitive import Integer
from spyne.model.primitive import Unicode
from spyne.model.complex import Iterable
from spyne.protocol.soap import Soap11
from spyne.server.wsgi import WsgiApplication
from spyne.protocol.http import HttpRpc
from spyne.server.wsgi import WsgiApplication
import psycopg2
from xml.dom import minidom
import traceback

def conexionPostgresPsycopg(csql):
    db = psycopg2.connect(host='10.160.1.229', database='prod_registrocivil', user='postgres', password='postgres', port='61432')
    c = db.cursor()
    c.execute(csql)
    db.commit()
    c.close()
    db.close()
    return

def conexionPsycopg(csql):
    db = psycopg2.connect(host='10.160.1.229', database='prod_registrocivil', user='postgres', password='postgres', port='61432')
    c = db.cursor()
    r=c.execute(csql)
    tupla = c.fetchall()
    c.close()
    db.close()
    return tupla

class Objetos(ComplexModel):
    nombre = Unicode
    ubicacion = Unicode

class RCWebService(ServiceBase):
    #@srpc(Unicode, Integer, _returns=Iterable(Unicode))
    #def diga_hola(name, times):
    #    for i in range(times):
    #        yield 'Hola, %s' % name

    #@srpc(Unicode, Unicode, Unicode, _returns=Iterable(Unicode))
    #def diga_otro_hola(cuit, cuil, xml):

    #    yield 'el cuit es %s'%cuit

    @srpc(Unicode, _returns=Iterable(Objetos))
    def nacimiento_propia(dni):

        csql = """
select i.ubicacion,i.nombre
from ciud_ciudadano_documento doc
join ciud_ciudadano_dato dato on dato.ciud_ciudadano_id = doc.ciud_ciudadano_id
join acta_acta_ciudadano ac on ac.ciud_ciudadano_dato_id = dato.id
--join acta_acta_ciudadano ac on ac.ciud_ciudadano_relacion_dato_id = dato.id
join acta_acta a on a.id = ac.acta_acta_id
join base_libro l on l.id = a.base_libro_id
join enlace_acta_imagen ei on ei.acta_acta_id = a.id
join enlace_imagen i on i.id = ei.enlace_imagen_id
where doc.numero = '%s'
and ac.base_tipo_rol_id = 3
and l.base_tipo_libro_id = 1
group by 1,2
                """%(dni)

        persona = conexionPsycopg(csql)
        valido = True
        resultado = 'correcto'
        print (persona)
        if len(persona)==0:
            resultado = 'error_persona'
            valido = False
        else:
#            fecha = str(persona[0][2].day).zfill(2)+"/"+str(persona[0][2].month).zfill(2)+"/"+str(persona[0][2].year) if persona[0][2]  else ''
            resultado = persona[0][0].decode('latin1') +" "+ persona[0][1].decode('latin1')# +"  "+ fecha
           # resultado2=[persona[0][0].decode('latin1'),persona[0][1].decode('latin1'), fecha ]

        print (resultado)
        #print resultado2
        #name = doc.getElementsByTagName("PERIODO")[0]
        #print(name.firstChild.data)
        array=[]
for f in persona:
                print (f)
                p = Objetos()
                p.nombre = str(f[1])
                p.ubicacion = str(f[0])
                array.append(p)
print (p)

application = Application([RCWebService],
    tns='coyote1.rc.mendoza.gov.ar',
    in_protocol=HttpRpc(validator='soft'),
    out_protocol=Soap11()
)
if __name__ == '__main__':

    from wsgiref.simple_server import make_server
    wsgi_app = WsgiApplication(application)
    server = make_server('localhost', 8000, wsgi_app)

